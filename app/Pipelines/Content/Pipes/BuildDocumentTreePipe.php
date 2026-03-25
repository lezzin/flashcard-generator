<?php

namespace App\Pipelines\Content\Pipes;

use App\DTOs\Content\DocumentNodeDto;
use App\DTOs\Parser\PdfElementDto;
use App\Models\BaseContentTree;
use App\Pipelines\Content\ContentPipelineContext;
use App\Services\Parser\ParserService;
use Closure;

class BuildDocumentTreePipe
{
    public function __construct(
        private ParserService $parserService
    ) {}

    public function handle(ContentPipelineContext $context, Closure $next)
    {
        $pdf = $this->parserService->handle($context->filePath);
        $context->documentTree = $this->buildTree($pdf->elements);

        $context->documentTreeId = BaseContentTree::create([
            'data' => json_encode($context->documentTree)
        ])->id;

        return $next($context);
    }

    private function buildTree(array $elements): array
    {
        $root = [];
        $stack = [];

        foreach ($elements as $element) {
            match ($element->type) {
                'heading' => $this->handleHeading($element, $root, $stack),
                'paragraph' => $this->handleParagraph($element, $stack, $root),
                'list' => $this->handleList($element, $stack, $root),
                default => null,
            };
        }

        return $root;
    }

    private function handleHeading(PdfElementDto $element, array &$root, array &$stack): void
    {
        $level = $element->extra['heading level'] ?? 1;

        $node = DocumentNodeDto::section(
            $element->content ?? 'Seção',
            $level
        );

        while (!empty($stack) && end($stack)->level >= $level) {
            array_pop($stack);
        }

        if (empty($stack)) {
            $root[] = $node;
        } else {
            end($stack)->children[] = $node;
        }

        $stack[] = $node;
    }

    private function handleParagraph(PdfElementDto $element, array &$stack, array &$root): void
    {
        if (!$element->content) return;

        $node = DocumentNodeDto::paragraph(trim($element->content));

        if (!empty($stack)) {
            end($stack)->children[] = $node;
        } else {
            $root[] = $node;
        }
    }

    private function handleList(PdfElementDto $element, array &$stack, array &$root): void
    {
        $items = [];

        foreach ($element->children as $child) {
            if ($child->content) {
                $items[] = trim($child->content);
            }
        }

        if (empty($items)) return;

        $node = DocumentNodeDto::list($items);

        if (!empty($stack)) {
            end($stack)->children[] = $node;
        } else {
            $root[] = $node;
        }
    }
}

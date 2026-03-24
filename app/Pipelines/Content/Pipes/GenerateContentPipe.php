<?php

namespace App\Pipelines\Content\Pipes;

use App\DTOs\Content\DocumentNodeDto;
use App\Jobs\Content\GenerateContentBatchJob;
use App\Pipelines\Content\ContentPipelineContext;
use Closure;

class GenerateContentPipe
{
    public function handle(ContentPipelineContext $context, Closure $next)
    {
        $this->processTree(
            nodes: $context->documentTree,
            documentTreeId: $context->documentTreeId
        );

        return $next($context);
    }

    private function processTree(array $nodes, int $documentTreeId, string $parentContext = ''): void
    {
        $hasSections = collect($nodes)->contains(fn($n) => $n->type === 'section');

        if (!$hasSections) {
            $this->processFlatParagraphs($nodes, $documentTreeId);
            return;
        }

        foreach ($nodes as $node) {
            if ($node->type !== 'section') {
                continue;
            }

            $newContext = $this->buildContext($parentContext, $node->title);
            $sectionText = $this->extractSectionText($node);

            if (!empty($sectionText)) {
                $chunks = $this->chunkText($sectionText);

                foreach ($chunks as $chunk) {
                    dispatch(new GenerateContentBatchJob(
                        chunk: $chunk,
                        documentTreeId: $documentTreeId,
                        newContext: $newContext
                    ))->onQueue('content:batch:generate');
                }
            }

            $this->processTree(
                nodes: $node->children,
                documentTreeId: $documentTreeId,
                parentContext: $newContext,
            );
        }
    }

    private function processFlatParagraphs(array $nodes, int $documentTreeId): void
    {
        foreach ($nodes as $node) {
            if ($node->type !== 'paragraph') {
                continue;
            }

            $text = $this->normalize($node->content);

            if (empty($text)) {
                continue;
            }

            dispatch(new GenerateContentBatchJob(
                chunk: [$text],
                documentTreeId: $documentTreeId,
            ))->onQueue('content:batch:generate');
        }
    }

    private function extractSectionText(DocumentNodeDto $node): string
    {
        $parts = [];

        foreach ($node->children as $child) {
            match ($child->type) {
                'paragraph' => $parts[] = $this->normalize($child->content),

                'list' => $parts = array_merge(
                    $parts,
                    array_map(
                        fn($item) => $this->normalize($item),
                        $child->items
                    )
                ),

                default => null,
            };
        }

        return trim(implode("\n", $parts));
    }

    private function chunkText(string $text, int $maxLines = 5): array
    {
        $lines = array_filter(
            array_map('trim', explode("\n", $text))
        );

        return array_chunk($lines, $maxLines);
    }

    private function buildContext(string $parent, string $current): string
    {
        return trim($parent . ' > ' . $current, ' >');
    }

    private function normalize(string $text): string
    {
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }
}

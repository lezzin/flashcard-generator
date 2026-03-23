<?php

namespace App\Pipelines\Content\Pipes;

use App\Actions\Gemini\GenerateJsonAction;
use App\DTOs\Content\DocumentNodeDto;
use App\Pipelines\Content\ContentPipelineContext;
use App\Prompts\ContentGeneratePrompt;
use Closure;
use Exception;
use Gemini\Data\Schema;
use Gemini\Enums\DataType;
use Illuminate\Support\Facades\Log;

class GenerateContentPipe
{
    public function __construct(
        private readonly GenerateJsonAction $generateJsonAction
    ) {}

    public function handle(ContentPipelineContext $context, Closure $next)
    {
        $schema = new Schema(
            type: DataType::OBJECT,
            properties: [
                'title' => new Schema(type: DataType::STRING),
                'summary' => new Schema(type: DataType::STRING),
            ],
            required: ['title', 'summary'],
        );

        $context->results = collect(
            $this->processTree($context->documentTree, $schema)
        )->filter();

        return $next($context);
    }

    private function processTree(array $nodes, Schema $schema, string $parentContext = ''): array
    {
        $results = [];

        $hasSections = collect($nodes)->contains(fn($n) => $n->type === 'section');

        if ($hasSections) {
            foreach ($nodes as $node) {
                if ($node->type !== 'section') {
                    continue;
                }

                $newContext = $this->buildContext($parentContext, $node->title);
                $sectionText = $this->extractSectionText($node);

                if (!empty($sectionText)) {
                    $chunks = $this->chunkText($sectionText);

                    foreach ($chunks as $chunk) {
                        try {
                            $results[] = $this->generateJsonAction->execute(
                                ContentGeneratePrompt::handle(
                                    $newContext,
                                    implode("\n", $chunk)
                                ),
                                $schema
                            );
                        } catch (Exception $e) {
                            Log::channel('content')->error(
                                "Failed to generate content: " . $e->getMessage()
                            );
                        }
                    }
                }

                $results = array_merge(
                    $results,
                    $this->processTree($node->children, $schema, $newContext)
                );
            }

            return $results;
        }

        return $this->processFlatParagraphs($nodes, $schema);
    }

    private function processFlatParagraphs(array $nodes, Schema $schema): array
    {
        $results = [];

        foreach ($nodes as $node) {
            if ($node->type !== 'paragraph') {
                continue;
            }

            $text = $this->normalize($node->content);

            if (empty($text)) {
                continue;
            }

            try {
                $results[] = $this->generateJsonAction->execute(
                    ContentGeneratePrompt::handle(null, $text),
                    $schema
                );
            } catch (Exception $e) {
                Log::channel('content')->error(
                    "Failed to generate content: " . $e->getMessage()
                );
            }
        }

        return $results;
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

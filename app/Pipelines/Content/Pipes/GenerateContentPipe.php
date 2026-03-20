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

        foreach ($nodes as $node) {
            if ($node->type === 'section') {
                $sectionText = $this->extractSectionText($node);

                if (!empty($sectionText)) {
                    try {
                        $results[] = $this->generateJsonAction->execute(
                            ContentGeneratePrompt::handle(
                                $parentContext ?: $node->title,
                                $sectionText
                            ),
                            $schema
                        );
                    } catch (Exception $e) {
                        //
                    }
                }

                $results = array_merge(
                    $results,
                    $this->processTree(
                        $node->children,
                        $schema,
                        $node->title
                    )
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
                'paragraph' => $parts[] = $child->content,
                'list' => $parts[] = implode("\n", array_map(
                    fn($item) => '• ' . $item,
                    $child->items
                )),
                default => null,
            };
        }

        return trim(implode("\n\n", $parts));
    }
}

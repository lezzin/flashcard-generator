<?php

namespace App\Pipelines\Content\Pipes;

use App\Actions\Gemini\GenerateJsonAction;
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
        $context->results = $context->blocks->map(function (array $block) {
            try {
                $schema = new Schema(
                    type: DataType::OBJECT,
                    properties: [
                        'title' => new Schema(type: DataType::STRING),
                        'content' => new Schema(type: DataType::STRING),
                    ],
                    required: ['title', 'content'],
                );

                return $this->generateJsonAction->execute(
                    ContentGeneratePrompt::handle(null, $block['content']),
                    $schema
                );
            } catch (Exception $e) {
                return [];
            }
        })->filter();

        return $next($context);
    }
}

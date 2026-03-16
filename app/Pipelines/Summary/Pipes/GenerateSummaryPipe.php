<?php

namespace App\Pipelines\Summary\Pipes;

use App\Actions\Gemini\GenerateJsonAction;
use App\Pipelines\Summary\SummaryPipelineContext;
use App\Prompts\SummaryGeneratePrompt;
use Closure;
use Exception;
use Gemini\Data\Schema;
use Gemini\Enums\DataType;

class GenerateSummaryPipe
{
    public function __construct(
        private readonly GenerateJsonAction $generateJsonAction
    ) {}

    public function handle(SummaryPipelineContext $context, Closure $next)
    {
        $context->results = $context->blocks->map(function (array $block) {
            try {
                $schema = new Schema(
                    type: DataType::OBJECT,
                    properties: [
                        'title' => new Schema(type: DataType::STRING),
                        'summary' => new Schema(type: DataType::STRING),
                    ],
                    required: ['title', 'summary'],
                );

                return $this->generateJsonAction->execute(
                    SummaryGeneratePrompt::handle(null, $block['content']),
                    $schema
                );
            } catch (Exception $e) {
                // If a block fails, we return a placeholder to avoid breaking the whole process
                // In a real scenario, we might want to log this or retry.
                return [
                    'title' => $block['title'] ?? 'Erro no processamento',
                    'summary' => 'Não foi possível gerar o resumo para este bloco: '.$e->getMessage(),
                ];
            }
        });

        return $next($context);
    }
}

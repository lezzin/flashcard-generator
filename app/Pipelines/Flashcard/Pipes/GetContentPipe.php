<?php

namespace App\Pipelines\Flashcard\Pipes;

use App\DTOs\SourceContentDto;
use App\Pipelines\Flashcard\FlashcardPipelineContext;
use Closure;
use Illuminate\Support\Str;

class GetContentPipe
{
    public function handle(FlashcardPipelineContext $context, Closure $next)
    {
        $context->log('GetContentPipe started');

        $rawSummaries = explode('{{FIM_RESUMO}}', trim($context->content));

        foreach ($rawSummaries as $summaryText) {
            $summaryText = trim(str_replace('{{INICIO_RESUMO}}', '', $summaryText));

            if (empty($summaryText)) {
                continue;
            }

            $parts = explode('{{FIM_TITULO_RESUMO}}', $summaryText);

            if (count($parts) < 2) {
                $context->log('Skipping malformed summary part', [
                    'part' => Str::limit($summaryText, 100),
                ]);

                continue;
            }

            $title = Str::replace('{{TITULO_RESUMO}}', '', trim($parts[0]));
            $body = trim($parts[1]);

            $context->log('Extracted summary source', [
                'title' => $title,
            ]);

            $context->sources->add(new SourceContentDto(
                title: $title,
                content: $body
            ));
        }

        $context->log('Finished extracting content', [
            'sources_count' => $context->sources->count(),
        ]);

        return $next($context);
    }
}

<?php

namespace App\Pipelines\Flashcard\Pipes;

use App\DTOs\RawFlashcardDto;
use App\Pipelines\Flashcard\FlashcardPipelineContext;
use Closure;
use Illuminate\Support\Str;

class GetContentPipe
{
    public function handle(FlashcardPipelineContext $context, Closure $next)
    {
        $content = trim($context->content);
        $rawSummaries = explode('{{FIM_RESUMO}}', $content);

        foreach ($rawSummaries as $summaryText) {
            $summaryText = trim(str_replace("{{INICIO_RESUMO}}", "", $summaryText));

            if (empty($summaryText)) {
                continue;
            }

            $parts = explode("{{FIM_TITULO_RESUMO}}", $summaryText);

            $title = Str::replace('{{TITULO_RESUMO}}', "", trim($parts[0]));
            $body = trim($parts[1]);

            $context->flashcards->add(new RawFlashcardDto(
                title: $title,
                content: $body
            ));
        }

        return $next($context);
    }
}

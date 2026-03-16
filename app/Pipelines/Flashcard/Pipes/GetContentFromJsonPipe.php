<?php

namespace App\Pipelines\Flashcard\Pipes;

use App\DTOs\GeneratedFlashcardDto;
use App\Enums\CardTypes;
use App\Pipelines\Flashcard\FlashcardPipelineContext;
use Closure;

class GetContentFromJsonPipe
{
    public function handle(FlashcardPipelineContext $context, Closure $next)
    {
        $parsedContent = json_decode($context->content);

        foreach ($parsedContent as $flashcard) {
            $context->results->add(new GeneratedFlashcardDto(
                CardTypes::tryFrom($flashcard->type),
                $flashcard->front,
                $flashcard->back ?? null,
                $flashcard->extra ?? null,
            ));
        }

        return $next($context);
    }
}

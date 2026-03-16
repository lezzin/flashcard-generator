<?php

namespace App\Pipelines\Flashcard\Pipes;

use App\DTOs\GeneratedFlashcardDto;
use App\Enums\CardType;
use App\Pipelines\Flashcard\FlashcardPipelineContext;
use Closure;

class GetContentFromJsonPipe
{
    public function handle(FlashcardPipelineContext $context, Closure $next)
    {
        $parsedContent = json_decode($context->content);

        foreach ($parsedContent as $flashcard) {
            $context->results->add(new GeneratedFlashcardDto(
                type: CardType::tryFrom($flashcard->type),
                deck: $flashcard->deck,
                front: $flashcard->front,
                back: $flashcard->back ?? null,
                extra: $flashcard->extra ?? null,
            ));
        }

        return $next($context);
    }
}

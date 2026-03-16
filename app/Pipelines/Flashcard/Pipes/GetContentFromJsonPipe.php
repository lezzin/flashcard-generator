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
        $context->log('GetContentFromJsonPipe started');

        $parsedContent = json_decode($context->content);

        if (is_array($parsedContent)) {
            foreach ($parsedContent as $flashcard) {
                $context->results->add(new GeneratedFlashcardDto(
                    type: CardType::tryFrom($flashcard->type),
                    deck: $flashcard->deck,
                    front: $flashcard->front,
                    back: $flashcard->back ?? null,
                    extra: $flashcard->extra ?? null,
                ));
            }

            $context->log('Parsed content from JSON successfully', [
                'count' => count($parsedContent)
            ]);
        } else {
            $context->log('Failed to parse content from JSON');
        }

        return $next($context);
    }
}

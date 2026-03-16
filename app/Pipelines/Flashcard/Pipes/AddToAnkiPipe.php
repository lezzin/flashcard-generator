<?php

namespace App\Pipelines\Flashcard\Pipes;

use App\Actions\Anki\CreateDeckAction;
use App\Actions\Anki\AddNotesAction;
use App\Actions\Flashcard\HighlightKeywordsAction;
use App\DTOs\GeneratedFlashcardDto;
use App\Enums\CardTypes;
use App\Pipelines\Flashcard\FlashcardPipelineContext;
use Closure;
use Illuminate\Support\Str;

class AddToAnkiPipe
{
    public function handle(FlashcardPipelineContext $context, Closure $next)
    {
        if ($context->results->isEmpty()) {
            return $next($context);
        }

        $payloads = $context->results
            ->map(fn($card) => $this->buildPayload($card));

        $improvedPayloads = $payloads
            ->chunk(50)
            ->flatMap(
                fn($chunk) => (new HighlightKeywordsAction)($chunk)
            );

        $deckName = $payloads->first()['deckName'] ?? 'Teste';

        (new CreateDeckAction)($deckName);
        (new AddNotesAction)($improvedPayloads->values()->toArray());

        return $next($context);
    }

    private function buildPayload(GeneratedFlashcardDto $flashcard): array
    {
        $fields = [
            'ID'    => Str::uuid()->toString(),
            'Extra' => $flashcard->extra,
        ];

        switch ($flashcard->type) {
            case CardTypes::CARD_OMIT:
                $fields['Texto'] = $flashcard->front;
                break;

            case CardTypes::CARD_SIMPLE:
                $fields['Frente'] = $flashcard->front;
                $fields['Verso']  = $flashcard->back;
                break;
        }

        return [
            'deckName'  => 'Teste',
            'modelName' => $flashcard->type->value,
            'tags'      => [],
            'fields'    => array_filter($fields, fn($v) => !is_null($v)),
        ];
    }
}

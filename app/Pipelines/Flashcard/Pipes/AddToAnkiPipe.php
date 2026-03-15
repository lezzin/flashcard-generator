<?php

namespace App\Pipelines\Flashcard\Pipes;

use App\Actions\Anki\AddFlashcardAction;
use App\DTOs\GeneratedFlashcardDto;
use App\Enums\CardTypes;
use App\Pipelines\Flashcard\FlashcardPipelineContext;
use Closure;
use Illuminate\Support\Str;

class AddToAnkiPipe
{
    public function handle(FlashcardPipelineContext $context, Closure $next)
    {
        $context->results->each(function ($flashcard) {
            (new AddFlashcardAction)(self::buildPayload($flashcard));
        });

        return $next($context);
    }

    private static function buildPayload(GeneratedFlashcardDto $flashcard): array
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

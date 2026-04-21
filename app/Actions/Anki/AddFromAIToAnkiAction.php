<?php

namespace App\Actions\Anki;

use App\Actions\Anki\Api\AddNotesAction;
use App\Actions\Anki\Api\CreateDeckAction;
use App\DTOs\GeneratedFlashcardDto;
use App\Enums\CardType;
use App\Mappers\FlashcardMapper;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AddFromAIToAnkiAction
{
    public function __construct(
        private readonly HighlightNoteAction $highlightNoteAction,
        private readonly CreateDeckAction $createDeckAction,
        private readonly AddNotesAction $addNotesAction,
    ) {
    }

    public function execute(Collection $flashcards): void
    {
        $payloads = $flashcards
            ->map(fn ($value) => FlashcardMapper::fromDatabaseToDto(is_array($value) ? (object) $value : $value))
            ->map(fn ($card) => $this->buildPayload($card));

        $improvedPayloads = $this->highlightNoteAction->execute($payloads);

        $uniqueDecks = $improvedPayloads
            ->pluck('deckName')
            ->unique();

        $uniqueDecks->each(
            fn ($deck) => $this->createDeckAction->execute($deck)
        );

        $this->addNotesAction->execute(
            $improvedPayloads->values()->toArray()
        );
    }

    private function buildPayload(GeneratedFlashcardDto $flashcard): array
    {
        $baseFields = [
            'ID'    => Str::uuid()->toString(),
            'Extra' => $flashcard->extra,
        ];

        $typeFields = match ($flashcard->type) {
            CardType::CLOZE => [
                'Texto' => $flashcard->front,
            ],

            CardType::SIMPLE => [
                'Frente' => $flashcard->front,
                'Verso'  => $flashcard->back,
            ],
        };

        return [
            'deckName'  => $flashcard->deck,
            'modelName' => $flashcard->type->value,
            'tags'      => [],
            'fields'    => Arr::whereNotNull([...$baseFields, ...$typeFields])
        ];
    }
}

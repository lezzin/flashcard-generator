<?php

namespace App\Actions\Anki;

use App\DTOs\GeneratedFlashcardDto;
use App\Enums\CardType;
use App\Mappers\FlashcardMapper;
use App\Models\AnkiFlashcard;
use App\Models\BaseContentTree;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AddFromDatabaseToAnkiAction
{
    private const CHUNK_SIZE = 50;

    public function __construct(
        private readonly HighlightNoteAction $highlightNoteAction,
        private readonly CreateDeckAction $createDeckAction,
        private readonly AddNotesAction $addNotesAction,
    ) {}

    public function execute(int $treeId): void
    {
        AnkiFlashcard::where('is_inserted', false)
            ->chunkById(
                self::CHUNK_SIZE,
                fn($flashcards) => $this->handleFlashcards($flashcards)
            );

        BaseContentTree::whereKey($treeId)
            ->update([
                'is_inserted' => true
            ]);
    }

    private function handleFlashcards(Collection $flashcards)
    {
        $payloads = $flashcards
            ->map(fn($value) => FlashcardMapper::fromDatabaseToDto($value))
            ->map(fn($card) => $this->buildPayload($card));

        $improvedPayloads = $this->highlightNoteAction->execute($payloads);

        $uniqueDecks = $improvedPayloads
            ->pluck('deckName')
            ->unique();

        $uniqueDecks->each(
            fn($deck) => $this->createDeckAction->execute($deck)
        );

        $this->addNotesAction->execute(
            $improvedPayloads->values()->toArray()
        );

        AnkiFlashcard::whereIn('id', $flashcards->pluck('id'))
            ->update(['is_inserted' => true]);
    }

    private function buildPayload(GeneratedFlashcardDto $flashcard): array
    {
        $fields = [
            'ID' => Str::uuid()->toString(),
            'Extra' => $flashcard->extra,
        ];

        switch ($flashcard->type) {
            case CardType::CLOZE:
                $fields['Texto'] = $flashcard->front;
                break;

            case CardType::SIMPLE:
                $fields['Frente'] = $flashcard->front;
                $fields['Verso'] = $flashcard->back;
                break;
        }

        return [
            'deckName' => $flashcard->deck,
            'modelName' => $flashcard->type->value,
            'tags' => [],
            'fields' => Arr::whereNotNull($fields),
        ];
    }
}

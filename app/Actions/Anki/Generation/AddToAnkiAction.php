<?php

namespace App\Actions\Anki\Generation;

use App\Actions\Anki\Notes\AddNotesAction;
use App\Actions\Anki\Decks\CreateDeckAction;
use App\Actions\Anki\Highlighting\HighlightNoteAction;
use App\DTOs\GeneratedFlashcardDto;
use App\Enums\CardType;
use App\Mappers\FlashcardMapper;
use App\Models\AnkiFlashcard;
use App\Models\BaseContentTree;
use Illuminate\Support\Str;

class AddToAnkiAction
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
            ->chunkById(self::CHUNK_SIZE, function ($flashcards) {
                $payloads = $flashcards
                    ->map(fn($value) => FlashcardMapper::fromDatabaseToDto($value))
                    ->map(fn($card) => $this->buildPayload($card));

                $improvedPayloads = $this->highlightNoteAction->execute($payloads);

                $uniqueDecks = $improvedPayloads
                    ->pluck('deckName')
                    ->unique();

                foreach ($uniqueDecks as $deck) {
                    $this->createDeckAction->execute($deck);
                }

                $this->addNotesAction->execute($improvedPayloads->values()->toArray());

                AnkiFlashcard::whereIn('id', $flashcards->pluck('id'))
                    ->update(['is_inserted' => true]);
            });

        BaseContentTree::whereKey($treeId)->update([
            'is_inserted' => true
        ]);
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
            'fields' => array_filter($fields, fn($v) => ! is_null($v)),
        ];
    }
}

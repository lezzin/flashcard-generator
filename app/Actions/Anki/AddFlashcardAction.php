<?php

namespace App\Actions\Anki;

use App\Actions\Flashcard\HighlightKeywordsAction;
use App\Enums\CardTypes;
use Illuminate\Support\Collection;

class AddFlashcardAction
{
    public function __construct(
        private readonly HighlightKeywordsAction $highlightKeywordsAction
    ) {}

    public function execute(Collection $payloads): array
    {
        if ($payloads->isEmpty()) {
            return [];
        }

        $deckName = $payloads->first()['deckName'] ?? 'Default';
        app(CreateDeckAction::class)->execute($deckName);

        $improvedNotes = $payloads->map(fn ($note) => $this->improveNote($note))->toArray();

        return app(AddNotesAction::class)->execute($improvedNotes);
    }

    private function improveNote(array $note): array
    {
        switch ($note['modelName']) {
            case CardTypes::CARD_OMIT->value:
                $note['fields']['Texto'] = $this->highlightKeywordsAction->execute($note['fields']['Texto']);
                break;

            case CardTypes::CARD_SIMPLE->value:
                $note['fields']['Frente'] = $this->highlightKeywordsAction->execute($note['fields']['Frente']);
                $note['fields']['Verso'] = $this->highlightKeywordsAction->execute($note['fields']['Verso']);
                break;
        }

        return $note;
    }
}

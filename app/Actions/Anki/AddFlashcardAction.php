<?php

namespace App\Actions\Anki;

use App\Actions\Flashcard\HighlightKeywordsAction;
use App\Enums\CardTypes;
use Illuminate\Support\Collection;

class AddFlashcardAction
{
    public function __invoke(Collection $payloads): array
    {
        if ($payloads->isEmpty()) {
            return [];
        }

        $deckName = $payloads->first()['deckName'] ?? 'Default';
        (new CreateDeckAction)($deckName);

        $improvedNotes = $payloads->map(fn($note) => $this->improveNote($note))->toArray();

        return (new AddNotesAction)($improvedNotes);
    }

    private function improveNote(array $note): array
    {
        switch ($note['modelName']) {
            case CardTypes::CARD_OMIT->value:
                $note['fields']['Texto'] = (new HighlightKeywordsAction)($note['fields']['Texto']);
                break;

            case CardTypes::CARD_SIMPLE->value:
                $note['fields']['Frente'] = (new HighlightKeywordsAction)($note['fields']['Frente']);
                $note['fields']['Verso'] = (new HighlightKeywordsAction)($note['fields']['Verso']);
                break;
        }

        return $note;
    }
}

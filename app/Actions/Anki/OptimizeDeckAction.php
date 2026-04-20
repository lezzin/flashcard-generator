<?php

namespace App\Actions\Anki;

use App\Actions\Anki\Api\DeleteNotesAction;
use App\Actions\Anki\Api\FindNotesByDeckNameAction;
use Illuminate\Support\Collection;

class OptimizeDeckAction extends BaseOptimizeAction
{
    public function execute(string $deckName, string $perPage, string $page, bool $filterByStyle): void
    {
        $currentNotesPage = app(FindNotesByDeckNameAction::class)->execute(
            deckName: $deckName,
            perPage: $perPage,
            page: $page,
            filterByStyle: $filterByStyle,
        );

        $processedNotes =  app(HighlightNoteAction::class)->execute(collect($currentNotesPage->items()));

        $invalidNotes = $processedNotes->filter(fn($note) => $note['invalid'] ?? false);
        $this->deleteNotes($invalidNotes);

        $validNotes = $processedNotes->reject(fn($note) => $note['invalid'] ?? false);
        $validNotes->each(fn($note) => $this->updateNoteIfHasFields($note));
    }

    protected function deleteNotes(Collection $notes): void
    {
        if ($notes->isEmpty()) {
            return;
        }

        $ids = $notes
            ->pluck('noteId')
            ->unique()
            ->values()
            ->toArray();

        app(DeleteNotesAction::class)->execute($ids);
    }
}

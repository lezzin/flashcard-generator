<?php

namespace App\Actions\Anki\Optimization;

use App\Actions\Anki\Notes\DeleteNotesAction;
use App\Actions\Anki\Notes\FindNotesByDeckNameAction;
use Illuminate\Support\Collection;

class OptimizeDeckAction extends BaseOptimizeAction
{
    public function execute(string $deckName, string $perPage, string $page): void
    {
        $currentNotesPage = app(FindNotesByDeckNameAction::class)->execute(
            $deckName,
            $perPage,
            $page
        );

        $processedNotes = $this->highlightNoteAction->execute(collect($currentNotesPage->items()));

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

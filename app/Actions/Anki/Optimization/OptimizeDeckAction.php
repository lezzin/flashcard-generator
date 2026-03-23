<?php

namespace App\Actions\Anki\Optimization;

use App\Actions\Anki\Notes\FindNotesByDeckNameAction;

class OptimizeDeckAction extends BaseOptimizeAction
{
    public function execute(string $deckName, string $perPage, string $page): void
    {
        $currentPage = app(FindNotesByDeckNameAction::class)->execute(
            $deckName,
            $perPage,
            $page
        );

        $notes = collect($currentPage->items());
        $improvedNotes = $this->highlightNoteAction->execute($notes);
        $improvedNotes->each(fn($note) => $this->updateNoteIfHasFields($note));
    }
}

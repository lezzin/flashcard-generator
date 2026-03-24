<?php

namespace App\Actions\Anki;

class OptimizeNoteAction extends BaseOptimizeAction
{
    public function execute(string $noteId): array
    {
        $note = app(FindNoteByIdAction::class)->execute((int) $noteId);
        $improved = app(HighlightNoteAction::class)->execute($note);

        $this->updateNoteIfHasFields($improved);

        return $improved;
    }
}

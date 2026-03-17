<?php

namespace App\Actions\Flashcard\Optimize;

use App\Actions\Anki\FindNoteByIdAction;

class OptimizeNoteAction extends BaseOptimizeAction
{
    public function execute(string $noteId): array
    {
        $findNoteByIdAction = app(FindNoteByIdAction::class);

        $note = $findNoteByIdAction->execute((int) $noteId);
        $improved = $this->highlightNoteAction->execute($note);

        $this->updateNoteIfHasFields($improved);

        return $improved;
    }
}

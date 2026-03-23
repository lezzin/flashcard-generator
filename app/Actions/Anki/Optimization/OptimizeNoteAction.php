<?php

namespace App\Actions\Anki\Optimization;

use App\Actions\Anki\Notes\FindNoteByIdAction;

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

<?php

namespace App\Actions\Anki;

use App\Actions\Anki\Api\FindNoteByIdAction;

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

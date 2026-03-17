<?php

namespace App\Actions\Flashcard;

use App\Actions\Anki\FindNoteByIdAction;
use App\Actions\Anki\UpdateNoteFieldsAction;

class ImproveFlashcardAction
{
    public function __construct(
        private readonly HighlightSingleAction $highlightSingleAction,
        private readonly UpdateNoteFieldsAction $updateNoteFieldsAction,
        private readonly FindNoteByIdAction $findNoteByIdAction,
    ) {}

    public function execute(string $noteId): array
    {
        $note = $this->findNoteByIdAction->execute($noteId);
        $improved = $this->highlightSingleAction->execute($note);

        $this->updateNoteIfHasFields($improved);

        return $improved;
    }

    private function updateNoteIfHasFields(array $note): void
    {
        $fields = $this->highlightSingleAction->extractFieldsToUpdate($note);

        if (empty($fields)) {
            return;
        }

        $this->updateNoteFieldsAction->execute($note['noteId'], $fields);
    }
}

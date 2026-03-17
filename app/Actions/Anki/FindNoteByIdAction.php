<?php

namespace App\Actions\Anki;

use App\Formatters\AnkiFormatter;
use Exception;

class FindNoteByIdAction
{
    public function __construct(
        private readonly InvokeAction $invokeAction,
    ) {}

    public function execute(int $noteId): array
    {
        $note = $this->invokeAction->execute('notesInfo', [
            'notes' => [$noteId],
        ])[0];

        if (empty($note)) {
            throw new Exception("Failed to get note with the provided ID.");
        }

        return AnkiFormatter::note($note);
    }
}

<?php

namespace App\Actions\Anki;

use App\Formatters\AnkiFormatter;
use Exception;

use App\Services\Anki\AnkiConnectClient;

class FindNoteByIdAction
{
    public function __construct(
        private readonly AnkiConnectClient $ankiClient,
    ) {}

    public function execute(int $noteId): array
    {
        $note = $this->ankiClient->invoke('notesInfo', [
            'notes' => [$noteId],
        ])[0];

        if (empty($note)) {
            throw new Exception("Failed to get note with the provided ID.");
        }

        return AnkiFormatter::note($note);
    }
}

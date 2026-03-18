<?php

namespace App\Actions\Anki;

use App\DTOs\Anki\NoteDto;
use App\Services\Anki\AnkiConnectClient;
use Exception;

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
            throw new Exception('Failed to get note with the provided ID.');
        }

        return NoteDto::fromRequest($note)->toArray();
    }
}

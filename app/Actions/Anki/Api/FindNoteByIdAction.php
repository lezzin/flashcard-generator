<?php

namespace App\Actions\Anki\Api;

use App\DTOs\Anki\NoteDto;
use App\Services\Anki\AnkiConnectClient;
use Exception;

class FindNoteByIdAction
{
    public function execute(int $noteId): array
    {
        $note = app(AnkiConnectClient::class)->invoke('notesInfo', [
            'notes' => [$noteId],
        ])[0] ?? null;

        if (empty($note)) {
            throw new Exception('Failed to get note with the provided ID.');
        }

        $deckNames = app(GetDeckNamesFromCardIdsAction::class)->execute($note['cards'] ?? []);

        return NoteDto::fromRequest($note)
            ->withDeckNames($deckNames)
            ->toArray();
    }
}

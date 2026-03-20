<?php

namespace App\Actions\Anki;

use App\DTOs\Anki\NoteDto;
use App\Services\Anki\AnkiConnectClient;
use Exception;

class FindNoteByIdAction
{
    public function __construct(
        private readonly AnkiConnectClient $ankiClient,
        private readonly GetDeckNamesFromCardIdsAction $getDeckNames,
    ) {}

    public function execute(int $noteId): array
    {
        $note = $this->ankiClient->invoke('notesInfo', [
            'notes' => [$noteId],
        ])[0] ?? null;

        if (empty($note)) {
            throw new Exception('Failed to get note with the provided ID.');
        }

        $deckNames = $this->getDeckNames->execute($note['cards'] ?? []);

        return NoteDto::fromRequest($note)
            ->withDeckNames($deckNames)
            ->toArray();
    }
}

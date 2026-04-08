<?php

namespace App\Actions\Anki\Api;

use App\DTOs\Anki\NoteDto;
use App\Services\Anki\AnkiConnectClient;
use Illuminate\Pagination\LengthAwarePaginator;

class FindNotesByDeckNameAction
{
    public function __construct(
        private readonly AnkiConnectClient $ankiClient,
        private readonly GetDeckNamesFromCardIdsAction $getDeckNames,
    ) {}

    public function execute(string $deckName, int $perPage = 100, int $page = 1): LengthAwarePaginator
    {
        $noteIds = $this->ankiClient->invoke('findNotes', [
            'query' => "\"deck:{$deckName}\"",
        ]);

        $offset = ($page - 1) * $perPage;
        $pagedNoteIds = array_slice($noteIds, $offset, $perPage);

        $noteInfos = $this->ankiClient->invoke('notesInfo', [
            'notes' => $pagedNoteIds,
        ]);

        $notes = collect($noteInfos)->map(fn($note) => $this->enrichAndFormatNote($note));

        return new LengthAwarePaginator(
            $notes,
            count($noteIds),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    private function enrichAndFormatNote(array $note)
    {
        $deckNames = $this->getDeckNames->execute($note['cards'] ?? []);

        return NoteDto::fromRequest($note)
            ->withDeckNames($deckNames)
            ->toArray();
    }
}

<?php

namespace App\Actions\Anki;

use App\Formatters\AnkiFormatter;
use Illuminate\Pagination\LengthAwarePaginator;

class FindNotesByDeckNameAction
{
    public function __construct(
        private readonly InvokeAction $invokeAction,
    ) {}

    public function execute(string $deckName, int $perPage = 100, int $page = 1): LengthAwarePaginator
    {
        $noteIds = $this->invokeAction->execute('findNotes', [
            'query' => "\"deck:{$deckName}\"",
        ]);

        $offset = ($page - 1) * $perPage;
        $pagedNoteIds = array_slice($noteIds, $offset, $perPage);

        $noteInfos = $this->invokeAction->execute('notesInfo', [
            'notes' => $pagedNoteIds,
        ]);

        $notes = collect($noteInfos)->map(fn ($note) => AnkiFormatter::note($note));

        return new LengthAwarePaginator(
            $notes,
            count($noteIds),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }
}

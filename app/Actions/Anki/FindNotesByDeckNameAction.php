<?php

namespace App\Actions\Anki;

use Illuminate\Support\Collection;

class FindNotesByDeckNameAction
{
    public function __construct(
        private readonly InvokeAction $invokeAction,
    ) {}

    public function execute(string $deckName): Collection
    {
        $noteIds = $this->invokeAction->execute('findNotes', [
            'query' => "\"deck:{$deckName}\""
        ]);

        $noteInfos = $this->invokeAction->execute('notesInfo', [
            'notes' => $noteIds
        ]);

        return collect($noteInfos)->map(function (array $note) {
            $note['fields'] = collect($note['fields'])
                ->mapWithKeys(fn($field, $name) => [$name => strip_tags($field['value'])])
                ->all();

            unset(
                $note['tags'],
                $note['profile'],
                $note['mod'],
                $note['cards'],
            );

            return $note;
        });
    }
}

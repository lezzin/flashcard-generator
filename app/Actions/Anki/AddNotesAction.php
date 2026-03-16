<?php

namespace App\Actions\Anki;

class AddNotesAction
{
    public function __invoke(array $notes): array
    {
        if (empty($notes)) {
            return [];
        }

        return (new InvokeAction)('addNotes', ['notes' => $notes]);
    }
}

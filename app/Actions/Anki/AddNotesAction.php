<?php

namespace App\Actions\Anki;

class AddNotesAction
{
    public function __construct(
        private readonly InvokeAction $invokeAction
    ) {}

    public function execute(array $notes): array
    {
        if (empty($notes)) {
            return [];
        }

        return $this->invokeAction->execute('addNotes', ['notes' => $notes]);
    }
}

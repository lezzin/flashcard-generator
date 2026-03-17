<?php

namespace App\Actions\Anki;

use App\Services\Anki\AnkiConnectClient;

class AddNotesAction
{
    public function __construct(
        private readonly AnkiConnectClient $ankiClient
    ) {}

    public function execute(array $notes): array
    {
        if (empty($notes)) {
            return [];
        }

        return $this->ankiClient->invoke('addNotes', ['notes' => $notes]);
    }
}

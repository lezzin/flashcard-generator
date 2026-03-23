<?php

namespace App\Actions\Anki\Notes;

use App\Services\Anki\AnkiConnectClient;

class DeleteNotesAction
{
    public function __construct(
        private readonly AnkiConnectClient $ankiClient
    ) {}

    public function execute(?array $notes): array|null
    {
        logger(json_encode($notes));
        return [];

        if (empty($notes)) {
            return [];
        }

        return $this->ankiClient->invoke('deleteNotes', ['notes' => $notes]);
    }
}

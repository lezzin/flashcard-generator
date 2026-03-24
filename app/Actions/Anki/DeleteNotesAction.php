<?php

namespace App\Actions\Anki;

use App\Services\Anki\AnkiConnectClient;

class DeleteNotesAction
{
    public function execute(?array $notes): void
    {
        if (empty($notes)) {
            return;
        }

        app(AnkiConnectClient::class)->invoke('deleteNotes', [
            'notes' => $notes
        ]);
    }
}

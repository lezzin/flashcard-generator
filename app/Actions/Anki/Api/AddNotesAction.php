<?php

namespace App\Actions\Anki\Api;

use App\Services\Anki\AnkiConnectClient;

class AddNotesAction
{
    public function execute(?array $notes): array
    {
        if (count($notes) === 0) {
            return [];
        }

        return app(AnkiConnectClient::class)->invoke('addNotes', [
            'notes' => $notes
        ]);
    }
}

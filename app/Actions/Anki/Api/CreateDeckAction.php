<?php

namespace App\Actions\Anki\Api;

use App\Services\Anki\AnkiConnectClient;

class CreateDeckAction
{
    public function execute(string $deckName): void
    {
        app(AnkiConnectClient::class)->invoke('createDeck', [
            'deck' => $deckName
        ]);
    }
}

<?php

namespace App\Actions\Anki;

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

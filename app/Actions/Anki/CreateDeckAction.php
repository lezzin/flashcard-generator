<?php

namespace App\Actions\Anki;

use App\Services\Anki\AnkiConnectClient;

class CreateDeckAction
{
    public function __construct(
        private readonly AnkiConnectClient $ankiClient
    ) {}

    public function execute(string $deckName): void
    {
        $this->ankiClient->invoke('createDeck', ['deck' => $deckName]);
    }
}

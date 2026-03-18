<?php

namespace App\Actions\Anki;

use App\DTOs\Anki\DeckDto;
use App\Services\Anki\AnkiConnectClient;

class GetDeckNamesAction
{
    public function __construct(
        private readonly AnkiConnectClient $ankiClient
    ) {}

    public function execute(): array
    {
        $deckNames = $this->ankiClient->invoke('deckNames');

        return collect($deckNames)
            ->map(
                fn($deck) => DeckDto::fromRequest($deck)->toArray()
            )
            ->toArray();
    }
}

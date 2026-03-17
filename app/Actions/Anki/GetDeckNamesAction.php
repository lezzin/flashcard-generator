<?php

namespace App\Actions\Anki;

use App\Formatters\AnkiFormatter;
use App\Services\Anki\AnkiConnectClient;

class GetDeckNamesAction
{
    public function __construct(
        private readonly AnkiConnectClient $ankiClient
    ) {}

    public function execute(): array
    {
        $deckNames = $this->ankiClient->invoke('deckNames');

        return collect($deckNames)->map(
            fn(string $deckName) => [
                "formatted" => AnkiFormatter::deckName($deckName),
                "raw"       => $deckName,
            ]
        )->toArray();
    }
}

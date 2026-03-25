<?php

namespace App\Actions\Anki\Api;

use App\DTOs\Anki\DeckDto;
use App\Services\Anki\AnkiConnectClient;

class GetDeckNamesAction
{
    public function __construct(
        private readonly AnkiConnectClient $ankiClient
    ) {}

    public function execute(bool $onlyFirstLevel = false): array
    {
        $deckNames = $this->ankiClient->invoke('deckNames');

        return collect($deckNames)
            ->filter(function ($deck) use ($onlyFirstLevel) {
                if (! $onlyFirstLevel) {
                    return true;
                }

                return substr_count($deck, '::') <= 1;
            })
            ->map(fn($deck) => DeckDto::fromRequest($deck)->toArray())
            ->values()
            ->toArray();
    }
}

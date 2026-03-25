<?php

namespace App\Actions\Anki\Api;

use App\Services\Anki\AnkiConnectClient;

class GetDeckNamesFromCardIdsAction
{
    public function __construct(
        private readonly AnkiConnectClient $ankiClient,
    ) {}

    public function execute(array $cardIds): array
    {
        if (empty($cardIds)) {
            return [];
        }

        $cardsInfo = $this->ankiClient->invoke('cardsInfo', [
            'cards' => array_values(array_unique($cardIds)),
        ]);

        return collect($cardsInfo)
            ->pluck('deckName')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}

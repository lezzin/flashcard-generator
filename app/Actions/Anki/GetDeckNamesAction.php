<?php

namespace App\Actions\Anki;

use App\Formatters\AnkiFormatter;

class GetDeckNamesAction
{
    public function __construct(
        private readonly InvokeAction $invokeAction
    ) {}

    public function execute()
    {
        $deckNames = $this->invokeAction->execute('deckNames');

        return collect($deckNames)->map(
            fn (string $deckName) => AnkiFormatter::deckName($deckName)
        );
    }
}

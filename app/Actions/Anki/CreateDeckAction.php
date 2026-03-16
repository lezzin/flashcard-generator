<?php

namespace App\Actions\Anki;

class CreateDeckAction
{
    public function __invoke(string $deckName): void
    {
        (new InvokeAction)('createDeck', ['deck' => $deckName]);
    }
}

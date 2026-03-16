<?php

namespace App\Actions\Anki;

class CreateDeckAction
{
    public function __construct(
        private readonly InvokeAction $invokeAction
    ) {}

    public function execute(string $deckName): void
    {
        $this->invokeAction->execute('createDeck', ['deck' => $deckName]);
    }
}

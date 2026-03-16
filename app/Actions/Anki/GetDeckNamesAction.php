<?php

namespace App\Actions\Anki;

class GetDeckNamesAction
{
    public function __construct(
        private readonly InvokeAction $invokeAction
    ) {}

    public function execute()
    {
        return $this->invokeAction->execute('deckNames');
    }
}

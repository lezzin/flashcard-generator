<?php

namespace App\Actions\Anki;

class AddFlashcardAction
{
    public function __invoke(array $cardConfig)
    {
        (new InvokeAction)('createDeck', ['deck' => $cardConfig['deckName']]);
        (new InvokeAction)('addNote', ['note' => $cardConfig]);
    }
}

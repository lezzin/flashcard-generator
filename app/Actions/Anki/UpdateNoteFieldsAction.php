<?php

namespace App\Actions\Anki;

use App\Services\Anki\AnkiConnectClient;

class UpdateNoteFieldsAction
{
    public function __construct(
        private readonly AnkiConnectClient $ankiClient
    ) {
    }

    public function execute(int $noteId, array $fields): void
    {
        $this->ankiClient->invoke('updateNoteFields', [
            'note' => [
                'id' => $noteId,
                'fields' => $fields,
            ],
        ]);
    }
}

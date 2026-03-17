<?php

namespace App\Actions\Anki;

class UpdateNoteFieldsAction
{
    public function __construct(
        private readonly InvokeAction $invokeAction
    ) {}

    public function execute(int $noteId, array $fields): void
    {
        $this->invokeAction->execute('updateNoteFields', [
            'note' => [
                'id' => $noteId,
                'fields' => $fields,
            ],
        ]);
    }
}

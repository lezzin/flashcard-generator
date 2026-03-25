<?php

namespace App\Actions\Anki;

use App\Actions\Anki\Api\UpdateNoteFieldsAction;
use App\Enums\CardType;

abstract class BaseOptimizeAction
{
    protected function updateNoteIfHasFields(array $note): void
    {
        if (isset($note['invalid']) && $note['invalid']) {
            return;
        }

        $fields = $this->extractFieldsToUpdate($note);

        if (empty($fields)) {
            return;
        }

        app(UpdateNoteFieldsAction::class)->execute((string) $note['noteId'], $fields);
    }

    protected function extractFieldsToUpdate(array $note): array
    {
        $type = CardType::tryFrom($note['modelName']);

        $fields = match ($type) {
            CardType::CLOZE => [
                'Texto' => $note['fields']['Texto'] ?? null,
                'Extra' => $note['fields']['Extra'] ?? null,
            ],
            CardType::SIMPLE => [
                'Frente' => $note['fields']['Frente'] ?? null,
                'Verso' => $note['fields']['Verso'] ?? null,
                'Extra' => $note['fields']['Extra'] ?? null,
            ],
            default => [],
        };

        return array_filter($fields);
    }
}

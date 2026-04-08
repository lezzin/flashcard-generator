<?php

namespace App\Actions\Anki;

use App\Actions\Anki\Api\UpdateNoteFieldsAction;
use App\Enums\CardType;
use App\Support\AnkiFieldNormalizer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
        $type = CardType::tryFrom($note['modelName'] ?? null);

        if ($type === null) {
            Log::channel('flashcard')->warning("Card type not mapped on application.", [
                'type' => $note['modelName'] ?? null,
                'note' => json_encode($note),
                'method' => 'BaseOptimizeAction::extractFieldsToUpdate'
            ]);
        }

        $noteFields = $note['fields'] ?? [];

        $fields = match ($type) {
            CardType::CLOZE => [
                'Texto' => $noteFields['Texto'] ?? null,
                'Extra' => $noteFields['Extra'] ?? null,
            ],
            CardType::SIMPLE => [
                'Frente' => $noteFields['Frente'] ?? null,
                'Verso'  => $noteFields['Verso'] ?? null,
                'Extra' => $noteFields['Extra'] ?? null,
            ],
            default => [],
        };

        if (array_key_exists('ID', $noteFields)) {
            $fields['ID'] = $noteFields['ID'] ?: Str::uuid()->toString();
        }

        return AnkiFieldNormalizer::prepareForUpdate($fields);
    }
}

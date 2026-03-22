<?php

namespace App\Actions\Flashcard\Optimize;

use App\Actions\Anki\UpdateNoteFieldsAction;
use App\Actions\Flashcard\Highlight\HighlightNoteAction;
use App\Enums\CardType;

abstract class BaseOptimizeAction
{
    public function __construct(
        protected readonly HighlightNoteAction $highlightNoteAction,
        protected readonly UpdateNoteFieldsAction $updateNoteFieldsAction,
    ) {}

    protected function updateNoteIfHasFields(array $note): void
    {
        $fields = $this->extractFieldsToUpdate($note);

        if (empty($fields)) {
            return;
        }

        $this->updateNoteFieldsAction->execute((string) $note['noteId'], $fields);
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

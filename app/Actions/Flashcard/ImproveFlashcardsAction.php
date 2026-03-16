<?php

namespace App\Actions\Flashcard;

use App\Actions\Anki\FindNotesByDeckNameAction;
use App\Actions\Anki\UpdateNoteFieldsAction;
use App\Actions\Flashcard\HighlightKeywordsAction;
use App\Enums\CardType;
use Exception;
use Illuminate\Support\Collection;

class ImproveFlashcardsAction
{
    public function __construct(
        private readonly FindNotesByDeckNameAction $findNotesByDeckNameAction,
        private readonly HighlightKeywordsAction $highlightKeywordsAction,
        private readonly UpdateNoteFieldsAction $updateNoteFieldsAction,
    ) {}

    public function execute(string $deckName): Collection
    {
        $notes = $this->findNotesByDeckNameAction->execute($deckName);

        if ($notes->isEmpty()) {
            throw new Exception("Nenhum registro encontrado para essa busca.");
        }

        $improvedNotes = $this->highlightKeywordsAction->execute($notes);

        $improvedNotes->each(function ($improvedNote) {
            $fields = [];

            $type = CardType::tryFrom($improvedNote['modelName']);

            match ($type) {
                CardType::CLOZE  => $fields['Texto']  = $improvedNote['fields']['Texto'] ?? null,
                CardType::SIMPLE => $fields['Frente'] = $improvedNote['fields']['Frente'] ?? null,
                default => null
            };

            $fields = array_filter($fields);

            if (empty($fields)) {
                throw new Exception("Erro ao obter campo a ser atualizado");
            }

            $this->updateNoteFieldsAction->execute(
                $improvedNote['noteId'],
                $fields
            );
        });

        return $improvedNotes;
    }
}

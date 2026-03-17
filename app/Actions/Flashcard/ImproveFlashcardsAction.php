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

    public function execute(string $deckName, int $perPage = 100): Collection
    {
        $allImprovedNotes = collect();

        $page = 1;
        $paginated = $this->findNotesByDeckNameAction->execute($deckName, $perPage, $page);

        if ($paginated->isEmpty()) {
            throw new Exception("Nenhum registro encontrado para essa busca.");
        }

        $totalPages = (int) ceil($paginated->total() / $perPage);

        do {
            $improvedNotes = $this->highlightKeywordsAction->execute(collect($paginated->items()));

            $improvedNotes->each(function ($note) {
                $fields = [];
                $type = CardType::tryFrom($note['modelName']);

                match ($type) {
                    CardType::CLOZE  => $fields['Texto']  = $note['fields']['Texto'] ?? null,
                    CardType::SIMPLE => $fields['Frente'] = $note['fields']['Frente'] ?? null,
                    default => null
                };

                $fields = array_filter($fields);

                if (!empty($fields)) {
                    $this->updateNoteFieldsAction->execute($note['noteId'], $fields);
                }
            });

            $allImprovedNotes = $allImprovedNotes->concat($improvedNotes);

            $page++;
            if ($page <= $totalPages) {
                $paginated = $this->findNotesByDeckNameAction->execute($deckName, $perPage, $page);
            }
        } while ($page <= $totalPages);

        return $allImprovedNotes;
    }
}

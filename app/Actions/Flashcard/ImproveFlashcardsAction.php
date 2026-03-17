<?php

namespace App\Actions\Flashcard;

use App\Actions\Anki\FindNotesByDeckNameAction;
use App\Actions\Anki\UpdateNoteFieldsAction;
use App\Actions\Flashcard\HighlightKeywordsAction;
use App\Enums\CardType;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
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
        $firstPage = $this->findNotesByDeckNameAction->execute($deckName, $perPage, page: 1);

        if ($firstPage->isEmpty()) {
            throw new Exception("Nenhum registro encontrado para essa busca.");
        }

        return $this->processAllPages($deckName, $perPage, $firstPage);
    }

    private function processAllPages(string $deckName, int $perPage, LengthAwarePaginator $firstPage): Collection
    {
        $totalPages   = (int) ceil($firstPage->total() / $perPage);
        $allImproved  = collect();
        $currentPage  = $firstPage;

        for ($page = 1; $page <= $totalPages; $page++) {
            if ($page > 1) {
                $currentPage = $this->findNotesByDeckNameAction->execute($deckName, $perPage, $page);
            }

            $allImproved = $allImproved->concat(
                $this->processPage($currentPage)
            );
        }

        return $allImproved;
    }

    private function processPage(LengthAwarePaginator $paginator): Collection
    {
        $notes         = collect($paginator->items());
        $improvedNotes = $this->highlightKeywordsAction->execute($notes);

        $improvedNotes->each(fn($note) => $this->updateNoteIfHasFields($note));

        return $improvedNotes;
    }

    private function updateNoteIfHasFields(array $note): void
    {
        $fields = $this->extractFields($note);

        if (empty($fields)) {
            return;
        }

        $this->updateNoteFieldsAction->execute($note['noteId'], $fields);
    }

    private function extractFields(array $note): array
    {
        $type = CardType::tryFrom($note['modelName']);

        $fields = match ($type) {
            CardType::CLOZE  => ['Texto'  => $note['fields']['Texto']  ?? null],
            CardType::SIMPLE => ['Frente' => $note['fields']['Frente'] ?? null],
            default          => [],
        };

        return array_filter($fields);
    }
}

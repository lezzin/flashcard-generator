<?php

namespace App\Actions\Flashcard\Optimize;

use App\Actions\Anki\FindNotesByDeckNameAction;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class OptimizeDeckAction extends BaseOptimizeAction
{
    public function execute(string $deckName, int $perPage = 100): Collection
    {
        $findNotesByDeckNameAction = app(FindNotesByDeckNameAction::class);

        $firstPage = $findNotesByDeckNameAction->execute($deckName, $perPage, page: 1);

        if ($firstPage->isEmpty()) {
            throw new Exception('Nenhum registro encontrado para essa busca.');
        }

        return $this->processAllPages($deckName, $perPage, $firstPage);
    }

    private function processAllPages(string $deckName, int $perPage, LengthAwarePaginator $firstPage): Collection
    {
        $findNotesByDeckNameAction = app(FindNotesByDeckNameAction::class);
        $totalPages = (int) ceil($firstPage->total() / $perPage);
        $allImproved = collect();
        $currentPage = $firstPage;

        for ($page = 1; $page <= $totalPages; $page++) {
            if ($page > 1) {
                $currentPage = $findNotesByDeckNameAction->execute($deckName, $perPage, $page);
            }

            $allImproved = $allImproved->concat(
                $this->processPage($currentPage)
            );
        }

        return $allImproved;
    }

    private function processPage(LengthAwarePaginator $paginator): Collection
    {
        $notes = collect($paginator->items());
        $improvedNotes = $this->highlightNoteAction->execute($notes);

        $improvedNotes->each(fn ($note) => $this->updateNoteIfHasFields($note));

        return $improvedNotes;
    }
}

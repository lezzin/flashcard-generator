<?php

namespace App\Actions\Flashcard\Optimize;

use App\Actions\Anki\FindNotesByDeckNameAction;
use App\Jobs\Deck\OptimizeDeckPageJob;
use Exception;

class DispatchOptimizeDeckAction extends BaseOptimizeAction
{
    public function execute(string $deckName, int $perPage = 50): void
    {
        $findNotesByDeckNameAction = app(FindNotesByDeckNameAction::class);
        $firstPage = $findNotesByDeckNameAction->execute($deckName, $perPage, page: 1);

        if ($firstPage->isEmpty()) {
            throw new Exception('Nenhum registro encontrado para essa busca.');
        }

        $totalPages = (int) ceil($firstPage->total() / $perPage);
        $this->dispatchJobs($deckName, $perPage, $totalPages);
    }

    private function dispatchJobs(string $deckName, int $perPage, int $totalPages): void
    {
        for ($page = 1; $page <= $totalPages; $page++) {
            dispatch(new OptimizeDeckPageJob(
                deckName: $deckName,
                perPage: $perPage,
                page: $page
            ))->onQueue('deck:batch:optimize');
        }
    }
}

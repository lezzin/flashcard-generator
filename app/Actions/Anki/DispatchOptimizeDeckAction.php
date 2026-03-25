<?php

namespace App\Actions\Anki;

use App\Actions\Anki\Api\FindNotesByDeckNameAction;
use App\Jobs\Deck\OptimizeDeckPageJob;
use Exception;

class DispatchOptimizeDeckAction extends BaseOptimizeAction
{
    public function execute(string $deckName, int $perPage = 100): void
    {
        $firstPage = app(FindNotesByDeckNameAction::class)->execute($deckName, $perPage);

        if ($firstPage->isEmpty()) {
            throw new Exception('No records found for this search.');
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

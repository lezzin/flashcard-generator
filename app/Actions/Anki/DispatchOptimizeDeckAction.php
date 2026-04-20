<?php

namespace App\Actions\Anki;

use App\Actions\Anki\Api\FindNotesByDeckNameAction;
use App\Jobs\Deck\OptimizeDeckPageJob;
use Exception;

class DispatchOptimizeDeckAction extends BaseOptimizeAction
{
    public function execute(string $deckName, int $perPage = 20, bool $filterByStyle = false): mixed
    {
        $firstPage = app(FindNotesByDeckNameAction::class)->execute(
            deckName: $deckName,
            perPage: $perPage,
            filterByStyle: $filterByStyle,
        );

        if ($firstPage->isEmpty()) {
            throw new Exception('No records found for this search.');
        }

        $totalPages = (int) ceil($firstPage->total() / $perPage);
        $this->dispatchJobs($deckName, $perPage, $totalPages, $filterByStyle);

        return $firstPage->total();
    }

    private function dispatchJobs(string $deckName, int $perPage, int $totalPages, bool $filterByStyle): void
    {
        for ($page = 1; $page <= $totalPages; $page++) {
            dispatch(new OptimizeDeckPageJob(
                deckName: $deckName,
                perPage: $perPage,
                page: $page,
                filterByStyle: $filterByStyle,
            ))->onQueue('deck:batch:optimize');
        }
    }
}

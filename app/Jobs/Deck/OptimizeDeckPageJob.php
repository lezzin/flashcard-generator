<?php

namespace App\Jobs\Deck;

use App\Actions\Anki\OptimizeDeckAction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class OptimizeDeckPageJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $deckName,
        private readonly string $perPage,
        private readonly string $page
    ) {}

    public function handle(OptimizeDeckAction $action): void
    {
        $action->execute($this->deckName, $this->perPage, $this->page);
    }
}

<?php

namespace App\Jobs\Deck;

use App\Actions\Flashcard\Optimize\OptimizeDeckAction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class OptimizeDeckPageJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        private readonly string $deckName,
        private readonly string $perPage,
        private readonly string $page
    ) {}

    public function backoff(): array
    {
        return [10, 30, 60];
    }

    public function handle(OptimizeDeckAction $action): void
    {
        $action->execute($this->deckName, $this->perPage, $this->page);
    }
}

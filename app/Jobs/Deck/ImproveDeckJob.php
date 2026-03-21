<?php

namespace App\Jobs\Deck;

use App\Actions\Flashcard\Optimize\OptimizeDeckAction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ImproveDeckJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $deckName,
    ) {}

    public function handle(OptimizeDeckAction $action): void
    {
        $action->execute($this->deckName);
    }
}

<?php

namespace App\Jobs\Deck;

use App\Actions\Flashcard\Optimize\DispatchOptimizeDeckAction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ImproveDeckJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $deckName,
    ) {}

    public function handle(DispatchOptimizeDeckAction $action): void
    {
        $action->execute($this->deckName);
    }
}

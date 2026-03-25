<?php

namespace App\Jobs\Flashcard;

use App\Actions\Anki\AddFromDatabaseToAnkiAction;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AddFlashcardToAnkiJob implements ShouldQueue
{
    use Queueable;
    use Batchable;

    public function __construct(
        private readonly int $treeId,
    ) {}

    public function handle(AddFromDatabaseToAnkiAction $action): void
    {
        $action->execute($this->treeId);
    }
}

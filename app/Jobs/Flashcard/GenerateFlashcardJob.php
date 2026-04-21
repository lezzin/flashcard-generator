<?php

namespace App\Jobs\Flashcard;

use App\Actions\Anki\GenerateContentAction;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateFlashcardJob implements ShouldQueue
{
    use Queueable;
    use Batchable;

    public function __construct(
        private readonly string $content,
    ) {}

    public function handle(GenerateContentAction $action): void
    {
        $action->execute($this->content);
    }
}

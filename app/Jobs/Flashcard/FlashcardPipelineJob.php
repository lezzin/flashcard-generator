<?php

namespace App\Jobs\Flashcard;

use App\Actions\Anki\GenerateFlashcardAction;
use App\DTOs\SourceContentDto;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FlashcardPipelineJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly SourceContentDto $source
    ) {}

    public function handle(GenerateFlashcardAction $action): void
    {
        $action->execute(source: $this->source);
    }
}

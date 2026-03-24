<?php

namespace App\Jobs\Flashcard;

use App\Actions\Anki\Generation\GenerateFlashcardAction;
use App\DTOs\SourceContentDto;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateFlashcardJob implements ShouldQueue
{
    use Queueable, Batchable;

    public function __construct(
        private readonly SourceContentDto $source,
        private readonly string $baseTitle,
        private readonly string $generationType,
    ) {}

    public function handle(GenerateFlashcardAction $action): void
    {
        $action->execute(
            source: $this->source,
            baseTitle: $this->baseTitle,
            generationType: $this->generationType
        );
    }
}

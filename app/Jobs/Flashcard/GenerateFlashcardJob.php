<?php

namespace App\Jobs\Flashcard;

use App\Actions\Anki\GenerateFlashcardAction;
use App\DTOs\SourceContentDto;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateFlashcardJob implements ShouldQueue
{
    use Queueable;
    use Batchable;

    public function __construct(
        private readonly SourceContentDto $dto,
    ) {}

    public function handle(GenerateFlashcardAction $action): void
    {
        $action->execute($this->dto);
    }
}

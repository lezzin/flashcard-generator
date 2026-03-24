<?php

namespace App\Jobs;

use App\Pipelines\Flashcard\FlashcardFromDeckPipeline;
use App\Pipelines\Flashcard\FlashcardPipeline;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateFlashcardsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly int $treeId,
        private readonly ?string $title = null,
        private readonly bool $fromDeck = false,
    ) {}

    public function handle(): void
    {
        if ($this->fromDeck) {
            FlashcardFromDeckPipeline::handle(
                treeId: $this->treeId,
                title: $this->title,
            );

            return;
        }

        FlashcardPipeline::handle(
            treeId: $this->treeId,
            title: $this->title,
        );
    }
}

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
            private readonly string $content,
            private readonly ?string $title = null,
            private readonly bool $isPath = false,
            private readonly bool $fromDeck = false,
        ) {}

        public function handle(): void
        {
            if ($this->fromDeck) {
                FlashcardFromDeckPipeline::handle(
                    content: $this->content,
                    title: $this->title,
                    isPath: $this->isPath
                );
                return;
            }

            FlashcardPipeline::handle(
                content: $this->content,
                title: $this->title,
                isPath: $this->isPath
            );
        }

}

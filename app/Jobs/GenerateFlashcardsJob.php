<?php

namespace App\Jobs;

use App\Pipelines\Flashcard\FlashcardPipeline;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateFlashcardsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $content,
        private readonly string $title,
    ) {}

    public function handle(): void
    {
        FlashcardPipeline::handle($this->content, $this->title);
    }
}

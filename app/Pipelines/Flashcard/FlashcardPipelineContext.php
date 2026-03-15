<?php

namespace App\Pipelines\Flashcard;

use Illuminate\Support\Collection;

class FlashcardPipelineContext
{
    /** @var Collection<string, \App\DTOs\RawFlashcardDto> */
    public Collection $flashcards;

    /** @var Collection<int, \App\DTOs\GeneratedFlashcardDto> */
    public Collection $results;

    public function __construct(
        public readonly string $content
    ) {
        $this->flashcards = collect();
        $this->results = collect();
    }
}

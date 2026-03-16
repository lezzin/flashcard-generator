<?php

namespace App\Pipelines\Flashcard;

use App\DTOs\GeneratedFlashcardDto;
use App\DTOs\RawFlashcardDto;
use Illuminate\Support\Collection;

class FlashcardPipelineContext
{
    /** @var Collection<string, RawFlashcardDto> */
    public Collection $flashcards;

    /** @var Collection<int, GeneratedFlashcardDto> */
    public Collection $results;

    public ?string $filename;

    public function __construct(
        public readonly string $content
    ) {
        $this->flashcards = collect();
        $this->results = collect();
    }
}

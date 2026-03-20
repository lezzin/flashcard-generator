<?php

namespace App\Pipelines\Flashcard;

use App\DTOs\GeneratedFlashcardDto;
use App\DTOs\SourceContentDto;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class FlashcardPipelineContext
{
    /** @var Collection<string, SourceContentDto> */
    public Collection $sources;

    /** @var Collection<int, GeneratedFlashcardDto> */
    public Collection $results;

    public ?string $filename;

    public function __construct(
        public readonly string $content,
        public readonly ?string $title = null,
        public readonly bool $isPath = false,
    ) {
        $this->sources = collect();
        $this->results = collect();
    }
}

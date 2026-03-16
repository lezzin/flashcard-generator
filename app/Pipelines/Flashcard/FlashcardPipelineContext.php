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
        public readonly string $title,
    ) {
        $this->sources = collect();
        $this->results = collect();
    }

    public function log(string $message, array $context = []): void
    {
        Log::channel('flashcard')->info($message, [
            'title' => $this->title,
            ...$context
        ]);
    }
}

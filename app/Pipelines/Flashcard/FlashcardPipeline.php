<?php

namespace App\Pipelines\Flashcard;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;

class FlashcardPipeline
{
    /**
     * @param string $content
     * @return Collection<int, \App\DTOs\GeneratedFlashcardDto>
     */
    public static function handle(string $content): Collection
    {
        $context = new FlashcardPipelineContext(content: $content);

        /** @var FlashcardPipelineContext $result */
        $result = app(Pipeline::class)
            ->send($context)
            ->through(self::pipes())
            ->thenReturn();

        return $result->results;
    }

    private static function pipes(): array
    {
        return [
            \App\Pipelines\Flashcard\Pipes\GetContentPipe::class,
            \App\Pipelines\Flashcard\Pipes\GenerateFlashcardPipe::class,
            \App\Pipelines\Flashcard\Pipes\SaveFlashcardResultPipe::class,
            \App\Pipelines\Flashcard\Pipes\AddToAnkiPipe::class,
        ];
    }
}

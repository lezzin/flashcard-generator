<?php

namespace App\Pipelines\Flashcard;

use App\Pipelines\Flashcard\Pipes\AddToAnkiPipe;
use App\Pipelines\Flashcard\Pipes\GetContentFromJsonPipe;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;

class FlashcardReprocessPipeline
{
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
            GetContentFromJsonPipe::class,
            AddToAnkiPipe::class,
        ];
    }
}

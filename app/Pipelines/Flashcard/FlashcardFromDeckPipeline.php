<?php

namespace App\Pipelines\Flashcard;

use App\Pipelines\Flashcard\Pipes\GenerateFlashcardFromDeckPipe;
use App\Pipelines\Flashcard\Pipes\GetContentPipe;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;

class FlashcardFromDeckPipeline
{
    public static function handle(int $treeId, ?string $title = null): void
    {
        $context = new FlashcardPipelineContext(title: $title, treeId: $treeId);

        app(Pipeline::class)
            ->send($context)
            ->through(self::pipes())
            ->thenReturn();
    }

    private static function pipes(): array
    {
        return [
            GetContentPipe::class,
            GenerateFlashcardFromDeckPipe::class,
        ];
    }
}

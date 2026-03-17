<?php

namespace App\Pipelines\Flashcard;

use App\Pipelines\Flashcard\Pipes\AddToAnkiPipe;
use App\Pipelines\Flashcard\Pipes\GenerateFlashcardPipe;
use App\Pipelines\Flashcard\Pipes\GetContentPipe;
use App\Pipelines\Flashcard\Pipes\SaveFlashcardResultPipe;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;

class FlashcardPipeline
{
    public static function handle(string $content, string $title): Collection
    {
        $context = new FlashcardPipelineContext(content: $content, title: $title);

        $context->log('Starting Flashcard Pipeline');

        /** @var FlashcardPipelineContext $result */
        $result = app(Pipeline::class)
            ->send($context)
            ->through(self::pipes())
            ->thenReturn();

        $context->log('Finished Flashcard Pipeline', [
            'total_flashcards' => $result->results->count(),
        ]);

        return $result->results;
    }

    private static function pipes(): array
    {
        return [
            GetContentPipe::class,
            GenerateFlashcardPipe::class,
            SaveFlashcardResultPipe::class,
            AddToAnkiPipe::class,
        ];
    }
}

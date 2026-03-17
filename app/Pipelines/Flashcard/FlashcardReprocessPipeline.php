<?php

namespace App\Pipelines\Flashcard;

use App\Pipelines\Flashcard\Pipes\AddToAnkiPipe;
use App\Pipelines\Flashcard\Pipes\GetContentFromJsonPipe;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;

class FlashcardReprocessPipeline
{
    public static function handle(string $content, string $title): Collection
    {
        $context = new FlashcardPipelineContext(content: $content, title: $title);

        $context->log('Starting Flashcard Reprocess Pipeline');

        /** @var FlashcardPipelineContext $result */
        $result = app(Pipeline::class)
            ->send($context)
            ->through(self::pipes())
            ->thenReturn();

        $context->log('Finished Flashcard Reprocess Pipeline', [
            'total_flashcards' => $result->results->count(),
        ]);

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

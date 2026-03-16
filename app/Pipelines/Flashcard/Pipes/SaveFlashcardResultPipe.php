<?php

namespace App\Pipelines\Flashcard\Pipes;

use App\Pipelines\Flashcard\FlashcardPipelineContext;
use Closure;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;

class SaveFlashcardResultPipe
{
    public function handle(FlashcardPipelineContext $context, Closure $next)
    {
        $context->filename = Date::now()->timestamp;

        $context->log('SaveFlashcardResultPipe started', [
            'filename' => $context->filename,
            'count' => $context->results->count()
        ]);

        $json = $context->results
            ->map(fn($card) => $card->toArray())
            ->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        Storage::disk('public')->put("flashcards/{$context->filename}.json", $json);

        $context->log('Saved flashcards to JSON file');

        return $next($context);
    }
}

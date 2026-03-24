<?php

namespace App\Pipelines\Flashcard\Pipes;

use App\Actions\Anki\Generation\AddToAnkiAction;
use App\DTOs\SourceContentDto;
use App\Jobs\Flashcard\GenerateFlashcardJob;
use App\Pipelines\Flashcard\FlashcardPipelineContext;
use Closure;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Throwable;

class GenerateFlashcardPipe
{
    public function handle(FlashcardPipelineContext $context, Closure $next)
    {
        $jobs = $context->sources->map(function (SourceContentDto $source) use ($context) {
            return new GenerateFlashcardJob(
                source: $source,
                baseTitle: $context->title,
                generationType: 'content',
            );
        });

        Bus::batch($jobs)
            ->name('flashcards-batch-generate')
            ->onQueue('flashcard:batch:generate')
            ->catch(function ($batch, Throwable $e) use ($context) {
                Log::channel('flashcard')->error('Error on batch processing: ' . $e->getMessage(), [
                    'batch_id' => $batch->id,
                    'tree_id' => $context->treeId,
                    'trace' => $e->getTrace(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
            })
            ->finally(function () use ($context) {
                app(AddToAnkiAction::class)->execute($context->treeId);
            })
            ->dispatch();

        return $next($context);
    }
}

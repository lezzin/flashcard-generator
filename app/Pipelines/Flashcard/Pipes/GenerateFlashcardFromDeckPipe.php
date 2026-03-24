<?php

namespace App\Pipelines\Flashcard\Pipes;

use App\Actions\Anki\Generation\AddToAnkiAction;
use App\Actions\Gemini\GenerateJsonAction;
use App\DTOs\SourceContentDto;
use App\Jobs\Flashcard\GenerateFlashcardJob;
use App\Pipelines\Flashcard\FlashcardPipelineContext;
use Closure;
use Illuminate\Support\Facades\Bus;

class GenerateFlashcardFromDeckPipe extends GenerateFlashcardPipe
{
    public function __construct(
        protected readonly GenerateJsonAction $generateJsonAction
    ) {}

    public function handle(FlashcardPipelineContext $context, Closure $next)
    {
        $jobs = $context->sources->map(function (SourceContentDto $source) use ($context) {
            return new GenerateFlashcardJob(
                source: $source,
                baseTitle: $context->title,
                generationType: 'deck',
            );
        });

        Bus::batch($jobs)
            ->name('flashcards-batch-generate')
            ->onQueue('flashcard:batch:generate')
            ->then(function () {
                app(AddToAnkiAction::class)->execute();
            })
            ->dispatch();

        return $next($context);
    }
}

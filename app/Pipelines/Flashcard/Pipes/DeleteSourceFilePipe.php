<?php

namespace App\Pipelines\Flashcard\Pipes;

use App\Pipelines\Flashcard\FlashcardPipelineContext;
use Closure;
use Illuminate\Support\Facades\Storage;

class DeleteSourceFilePipe
{
    public function handle(FlashcardPipelineContext $context, Closure $next)
    {
        try {
            return $next($context);
        } finally {
            if ($context->isPath && Storage::disk()->exists($context->content)) {
                Storage::disk()->delete($context->content);
            }
        }
    }
}

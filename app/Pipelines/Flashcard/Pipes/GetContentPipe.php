<?php

namespace App\Pipelines\Flashcard\Pipes;

use App\DTOs\SourceContentDto;
use App\Models\GeneratedContent;
use App\Pipelines\Flashcard\FlashcardPipelineContext;
use Closure;

class GetContentPipe
{
    public function handle(FlashcardPipelineContext $context, Closure $next)
    {
        $generatedContents = GeneratedContent::query()
            ->where('tree_id', $context->treeId)
            ->whereHas('tree', fn($q) => $q->where('is_inserted', false))
            ->select('title', 'description')
            ->get()
            ->toArray();

        foreach ($generatedContents as $content) {
            if (!isset($content['title'], $content['description'])) {
                continue;
            }

            $context->sources->add(new SourceContentDto(
                title: $content['title'],
                content: $content['description'],
            ));
        }

        return $next($context);
    }
}

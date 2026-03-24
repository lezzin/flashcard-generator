<?php

namespace App\Pipelines\Content\Pipes;

use App\Models\GeneratedContent;
use App\Pipelines\Content\ContentPipelineContext;
use Closure;

class SaveContentResultPipe
{
    public function handle(ContentPipelineContext $context, Closure $next)
    {
        GeneratedContent::insert(
            $context->results->map(function ($content) use ($context) {
                return [
                    'title'       => $content->title,
                    'description' => $content->summary,
                    'tree_id'     => $context->documentTreeId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray()
        );

        return $next($context);
    }
}

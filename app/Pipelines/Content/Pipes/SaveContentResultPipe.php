<?php

namespace App\Pipelines\Content\Pipes;

use App\Pipelines\Content\ContentPipelineContext;
use Closure;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;

class SaveContentResultPipe
{
    public function handle(ContentPipelineContext $context, Closure $next)
    {
        $filename = Date::now()->timestamp;

        $json = collect($context->results)->toJson(
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );

        Storage::disk('public')->put("contents/{$filename}.json", $json);

        return $next($context);
    }
}

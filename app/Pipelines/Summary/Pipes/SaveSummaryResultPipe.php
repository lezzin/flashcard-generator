<?php

namespace App\Pipelines\Summary\Pipes;

use App\Pipelines\Summary\SummaryPipelineContext;
use Closure;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;

class SaveSummaryResultPipe
{
    public function handle(SummaryPipelineContext $context, Closure $next)
    {
        $filename = Date::now()->timestamp;

        $json = collect($context->results)->toJson(
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );

        Storage::disk('public')->put("{$filename}.json", $json);

        return $next($context);
    }
}

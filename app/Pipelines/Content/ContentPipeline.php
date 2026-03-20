<?php

namespace App\Pipelines\Content;

use App\Pipelines\Content\Pipes\BuildDocumentTreePipe;
use App\Pipelines\Content\Pipes\GenerateContentPipe;
use App\Pipelines\Content\Pipes\GetContentPipe;
use App\Pipelines\Content\Pipes\SaveContentResultPipe;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;

class ContentPipeline
{
    public static function handle(string $filePath): Collection
    {
        $context = new ContentPipelineContext(filePath: $filePath);

        /** @var ContentPipelineContext $result */
        $result = app(Pipeline::class)
            ->send($context)
            ->through(self::pipes())
            ->thenReturn();

        return $result->results;
    }

    private static function pipes(): array
    {
        return [
            GetContentPipe::class,
            BuildDocumentTreePipe::class,
            GenerateContentPipe::class,
            SaveContentResultPipe::class,
        ];
    }
}

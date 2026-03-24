<?php

namespace App\Pipelines\Content;

use App\Pipelines\Content\Pipes\BuildDocumentTreePipe;
use App\Pipelines\Content\Pipes\GenerateContentPipe;
use Illuminate\Pipeline\Pipeline;

class ContentPipeline
{
    public static function handle(string $filePath): void
    {
        $context = new ContentPipelineContext(filePath: $filePath);

        app(Pipeline::class)
            ->send($context)
            ->through(self::pipes())
            ->thenReturn();
    }

    private static function pipes(): array
    {
        return [
            BuildDocumentTreePipe::class,
            GenerateContentPipe::class,
        ];
    }
}

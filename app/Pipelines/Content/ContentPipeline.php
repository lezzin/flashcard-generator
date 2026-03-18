<?php

namespace App\Pipelines\Content;

use App\Pipelines\Content\Pipes\GenerateContentPipe;
use App\Pipelines\Content\Pipes\GetContentPipe;
use App\Pipelines\Content\Pipes\SaveContentResultPipe;
use App\Pipelines\Content\Pipes\StructureStudyContentPipe;
use Illuminate\Http\UploadedFile;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;

class ContentPipeline
{
    public static function handle(UploadedFile $file): Collection
    {
        $context = new ContentPipelineContext(file: $file);

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
            StructureStudyContentPipe::class,
            GenerateContentPipe::class,
            SaveContentResultPipe::class,
        ];
    }
}

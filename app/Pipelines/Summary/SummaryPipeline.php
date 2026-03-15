<?php

namespace App\Pipelines\Summary;

use App\Pipelines\Summary\Pipes\GenerateSummaryPipe;
use App\Pipelines\Summary\Pipes\GetContentPipe;
use App\Pipelines\Summary\Pipes\SaveSummaryResultPipe;
use App\Pipelines\Summary\Pipes\StructureStudyContentPipe;
use Illuminate\Http\UploadedFile;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;

class SummaryPipeline
{
    /**
     * @param UploadedFile $file
     * @return Collection<int, array{title: string, summary: string}>
     */
    public static function handle(UploadedFile $file): Collection
    {
        $context = new SummaryPipelineContext(file: $file);

        /** @var SummaryPipelineContext $result */
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
            GenerateSummaryPipe::class,
            SaveSummaryResultPipe::class
        ];
    }
}

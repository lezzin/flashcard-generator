<?php

namespace App\Jobs;

use App\Pipelines\Content\ContentPipeline;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\UploadedFile;

class GenerateContentJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $filePath,
    ) {}

    public function handle(): void
    {
        ContentPipeline::handle($this->filePath);
    }
}

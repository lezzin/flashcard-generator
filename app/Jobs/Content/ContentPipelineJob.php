<?php

namespace App\Jobs\Content;

use App\Pipelines\Content\ContentPipeline;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ContentPipelineJob implements ShouldQueue
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

<?php

namespace App\Pipelines\Content;

use Illuminate\Support\Collection;

class ContentPipelineContext
{
    public Collection $results;

    public array $documentTree = [];

    public int $documentTreeId;

    public function __construct(
        public readonly string $filePath
    ) {
        $this->results = collect();
    }
}

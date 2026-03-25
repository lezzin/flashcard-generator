<?php

namespace App\Pipelines\Content;

class ContentPipelineContext
{
    public array $documentTree = [];

    public int $documentTreeId;

    public function __construct(
        public readonly string $filePath
    ) {
    }
}

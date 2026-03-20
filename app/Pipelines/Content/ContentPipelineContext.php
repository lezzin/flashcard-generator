<?php

namespace App\Pipelines\Content;

use App\DTOs\Parser\PdfDataDto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class ContentPipelineContext
{
    public PdfDataDto $pdf;

    public Collection $results;

    public array $documentTree = [];

    public function __construct(
        public readonly string $filePath
    ) {
        $this->results = collect();
    }
}

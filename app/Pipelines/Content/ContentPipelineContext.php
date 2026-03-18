<?php

namespace App\Pipelines\Content;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class ContentPipelineContext
{
    /**
     * @var Collection<int, array{title: ?string, content: string}>
     */
    public Collection $blocks;

    public ?string $content;

    /**
     * @var Collection<int, array{title: string, content: string}>
     */
    public Collection $results;

    public function __construct(
        public readonly UploadedFile $file
    ) {
        $this->blocks = collect();
        $this->results = collect();
    }

    public function cleanContent(): void
    {
        $this->content = null;
    }
}

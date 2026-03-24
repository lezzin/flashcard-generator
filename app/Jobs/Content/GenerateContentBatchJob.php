<?php

namespace App\Jobs\Content;

use App\Actions\Anki\GenerateContentAction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateContentBatchJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly array $chunk,
        private readonly int $documentTreeId,
        private readonly ?string $newContext = null,
    ) {}

    public function handle(GenerateContentAction $action): void
    {
        $action->execute($this->chunk, $this->documentTreeId, $this->newContext);
    }
}

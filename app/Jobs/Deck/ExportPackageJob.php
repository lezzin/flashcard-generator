<?php

namespace App\Jobs\Deck;

use App\Actions\Anki\ExportPackageToDriveAction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExportPackageJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly ?string $deckName = null,
    ) {}

    public function handle(ExportPackageToDriveAction $action): void
    {
        $action->execute($this->deckName);
    }
}

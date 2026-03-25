<?php

namespace App\Jobs\Deck;

use App\Actions\Anki\ExportPackageAction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExportPackageJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly ?string $deckName = null,
    ) {
    }

    public function handle(ExportPackageAction $action): void
    {
        $action->execute($this->deckName);
    }
}

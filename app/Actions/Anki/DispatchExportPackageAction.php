<?php

namespace App\Actions\Anki;

use App\Jobs\Deck\ExportPackageJob;

class DispatchExportPackageAction
{
    public function execute(?string $deckName = null): void
    {
        $decksToProcess = $deckName
            ? collect([['raw' => $deckName]])
            : collect(app(GetDeckNamesAction::class)->execute(true));

        foreach ($decksToProcess as $deck) {
            $rawName = $deck['raw'];

            dispatch(
                new ExportPackageJob(deckName: $rawName)
            )->onQueue('deck:batch:export');
        }
    }
}

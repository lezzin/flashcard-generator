<?php

namespace App\Actions\Anki\Api;

use App\Helpers\File;
use App\Services\Anki\AnkiConnectClient;
use Exception;

class ExportPackageAction
{
    public function execute(string $deckName, string $path): string
    {
        $linuxPath = File::convertWindowsToLinuxPath($path);

        app(AnkiConnectClient::class)->invoke('exportPackage', [
            'deck' => $deckName,
            'path' => $path,
            'includeSched' => false,
        ]);

        if (! file_exists($linuxPath)) {
            throw new Exception("Export file was not created by Anki at: {$path}");
        }

        return $linuxPath;
    }
}

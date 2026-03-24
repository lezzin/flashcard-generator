<?php

namespace App\Actions\Anki;

use App\Actions\Google\UploadFileAction;
use App\Helpers\File;
use App\Services\Anki\AnkiConnectClient;
use Exception;
use Illuminate\Support\Str;

class ExportPackageAction
{
    public function __construct(
        private readonly AnkiConnectClient $ankiClient,
    ) {}

    public function execute(string $deckName): void
    {
        $windowsLocalFilePath = $this->buildFilePath($deckName);
        $linuxLocalFilePath = File::convertWindowsToLinuxPath($windowsLocalFilePath);

        $this->ankiClient->invoke('exportPackage', [
            'deck' => $deckName,
            'path' => $windowsLocalFilePath,
            'includeSched' => false,
        ]);

        if (! file_exists($linuxLocalFilePath)) {
            throw new Exception("Export file was not created by Anki at: {$windowsLocalFilePath}");
        }

        $this->exportToDrive($linuxLocalFilePath, $deckName);
    }

    private function exportToDrive(string $linuxLocalFilePath, string $deckName)
    {
        try {
            $uploadedFile = File::wrapInUploadedFile($linuxLocalFilePath);
            app(UploadFileAction::class)->execute($uploadedFile, $deckName);
        } finally {
            File::deleteIfExists($linuxLocalFilePath);
        }
    }

    private function buildFilePath(string $deckName): string
    {
        $safeName = Str::slug($deckName, '_');

        return "C:/Anki/{$safeName}_" . time() . ".apkg";
    }
}

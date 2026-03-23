<?php

namespace App\Actions\Anki\Export;

use App\Actions\Google\Drive\UploadFileAction;
use App\Helpers\File;
use App\Services\Anki\AnkiConnectClient;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ExportPackageAction
{
    public function __construct(
        private readonly AnkiConnectClient $ankiClient,
        private readonly UploadFileAction $uploadFileAction
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
            throw new \Exception("Export file was not created by Anki at: {$windowsLocalFilePath}");
        }

        try {
            $uploadedFile = $this->wrapInUploadedFile($linuxLocalFilePath);
            $this->uploadFileAction->execute($uploadedFile, $deckName);
        } finally {
            if (!file_exists($linuxLocalFilePath)) {
                return;
            }

            @unlink($linuxLocalFilePath);
        }
    }

    private function buildFilePath(string $deckName): string
    {
        $safeName = Str::slug($deckName, '_');

        return "C:/Anki/{$safeName}_" . time() . ".apkg";
    }

    private function wrapInUploadedFile(string $path): UploadedFile
    {
        return new UploadedFile(
            $path,
            basename($path),
            'application/octet-stream',
            null,
            true
        );
    }
}

<?php

namespace App\Actions\Anki;

use App\Actions\Google\UploadFileAction;
use App\Services\Anki\AnkiConnectClient;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ExportPackageAction
{
    private string $tempPath;

    public function __construct(
        private readonly AnkiConnectClient $ankiClient,
        private readonly GetDeckNamesAction $getDeckNamesAction,
        private readonly UploadFileAction $uploadFileAction
    ) {
        $this->tempPath = storage_path('app/temp_exports');
    }

    public function execute(?string $deckName = null): array
    {
        $this->ensureTempDirectoryExists();

        $results = [
            'success' => [],
            'failed' => [],
        ];

        $decksToProcess = $deckName
            ? collect([['raw' => $deckName]])
            : collect($this->getDeckNamesAction->execute());

        foreach ($decksToProcess as $deck) {
            $rawName = $deck['raw'];

            try {
                $this->processDeck($rawName);
                $results['success'][] = $rawName;
            } catch (Throwable $e) {
                Log::error("Failed to export deck: {$rawName}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                $results['failed'][] = $rawName;
            }
        }

        return $results;
    }

    private function processDeck(string $deckName): void
    {
        $localFilePath = $this->buildFilePath($deckName);

        $this->ankiClient->invoke('exportPackage', [
            'deck' => $deckName,
            'path' => $localFilePath,
            'includeSched' => false,
        ]);

        if (! file_exists($localFilePath)) {
            throw new \Exception("Export file was not created by Anki at: {$localFilePath}");
        }

        try {
            $uploadedFile = $this->wrapInUploadedFile($localFilePath);
            $this->uploadFileAction->execute($uploadedFile, $deckName);
        } finally {
            if (file_exists($localFilePath)) {
                @unlink($localFilePath);
            }
        }
    }

    private function ensureTempDirectoryExists(): void
    {
        if (! is_dir($this->tempPath)) {
            mkdir($this->tempPath, 0755, true);
        }
    }

    private function buildFilePath(string $deckName): string
    {
        $safeName = Str::slug($deckName, '_');

        return $this->tempPath.DIRECTORY_SEPARATOR."{$safeName}_".time().'.apkg';
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

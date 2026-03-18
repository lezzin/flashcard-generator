<?php

namespace App\Actions\Anki;

use App\Actions\Google\UploadFileAction;
use App\Services\Anki\AnkiConnectClient;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ExportPackageAction
{
    private const PATH = 'C:\\AnkiExports';

    public function __construct(
        private readonly AnkiConnectClient $ankiClient
    ) {}

    public function execute(string $deckName): string
    {
        $newName = $this->formatDeckName($deckName);
        $path = $this->buildPath($newName);

        $this->prepareDeck($deckName, $newName);
        $this->exportDeck($newName, $path);

        try {
            $file = $this->makeUploadedFile($path, $newName);

            return app(UploadFileAction::class)->execute($file);
        } finally {
            $this->deleteFile($path);
        }
    }

    private function formatDeckName(string $deckName): string
    {
        return "MeF - {$deckName}";
    }

    private function buildPath(string $deckName): string
    {
        $safeName = Str::slug($deckName, '_');
        return self::PATH . DIRECTORY_SEPARATOR . "{$safeName}.apkg";
    }

    private function prepareDeck(string $original, string $new): void
    {
        $this->ankiClient->invoke("createDeck", [
            "deck" => $new,
        ]);

        $cardIds = $this->ankiClient->invoke("findCards", [
            "query" => "deck:\"{$original}\""
        ]);

        if (empty($cardIds)) {
            return;
        }

        $this->ankiClient->invoke("changeDeck", [
            "cards" => $cardIds,
            "deck" => $new,
        ]);
    }

    private function exportDeck(string $deckName, string $path): void
    {
        $this->ankiClient->invoke("exportPackage", [
            "deck" => $deckName,
            "path" => $path,
            "includeSched" => false,
        ]);
    }

    private function makeUploadedFile(string $path, string $deckName): UploadedFile
    {
        return new UploadedFile(
            $path,
            basename($path),
            'application/octet-stream',
            null,
            true
        );
    }

    private function deleteFile(string $path): void
    {
        if (file_exists($path)) {
            @unlink($path);
        }
    }
}

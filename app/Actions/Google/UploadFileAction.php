<?php

namespace App\Actions\Google;

use App\Services\Google\GoogleAuthService;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class UploadFileAction
{
    private array $folderCache = [];

    public function __construct(
        private readonly GoogleAuthService $authService
    ) {}

    public function execute(UploadedFile $file, string $deckName): string
    {
        $drive = new Drive($this->authService->getAuthenticatedClient());

        $hierarchy = $this->parseDeckHierarchy($deckName);
        $parentId = config('filesystems.disks.google.folderId');

        foreach ($hierarchy['folders'] as $folderName) {
            $parentId = $this->getOrCreateFolder($drive, $folderName, $parentId);
        }

        $fileMetadata = new DriveFile([
            'name' => $hierarchy['fileName'],
            'parents' => [$parentId],
        ]);

        $content = file_get_contents($file->getRealPath());

        $uploaded = $drive->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => $file->getClientMimeType(),
            'uploadType' => 'multipart',
            'fields' => 'id',
        ]);

        return $uploaded->id;
    }

    private function parseDeckHierarchy(string $deckName): array
    {
        $parts = explode('::', $deckName);
        $mainDeck = $parts[0];
        $folders = [$mainDeck];

        $fileName = Str::slug($mainDeck) . '.apkg';

        if (count($parts) > 1) {
            $folders[] = 'Separados';

            $subName = $parts[1];
            $firstSub = explode('::', $subName)[0];
            $fileName = Str::slug(Str::before($firstSub, ' ')) . '.apkg';
        }

        return [
            'folders' => $folders,
            'fileName' => $fileName,
        ];
    }

    private function getOrCreateFolder(Drive $drive, string $name, string $parentId): string
    {
        $cacheKey = "{$parentId}_{$name}";
        if (isset($this->folderCache[$cacheKey])) {
            return $this->folderCache[$cacheKey];
        }

        $query = sprintf(
            "name = '%s' and mimeType = 'application/vnd.google-apps.folder' and '%s' in parents and trashed = false",
            str_replace("'", "\\'", $name),
            $parentId
        );

        $results = $drive->files->listFiles([
            'q' => $query,
            'fields' => 'files(id, name)',
            'pageSize' => 1,
        ]);

        if (count($results->files) > 0) {
            return $this->folderCache[$cacheKey] = $results->files[0]->id;
        }

        $folderMetadata = new DriveFile([
            'name' => $name,
            'mimeType' => 'application/vnd.google-apps.folder',
            'parents' => [$parentId],
        ]);

        $folder = $drive->files->create($folderMetadata, [
            'fields' => 'id',
        ]);

        return $this->folderCache[$cacheKey] = $folder->id;
    }
}

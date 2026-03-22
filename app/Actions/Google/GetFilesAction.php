<?php

namespace App\Actions\Google;

use App\DTOs\Google\DriveFileDto;
use App\Services\Google\GoogleAuthService;
use Google\Service\Drive;
use Google\Service\Drive\FileList;

class GetFilesAction
{
    public function __construct(
        private readonly GoogleAuthService $authService
    ) {}

    public function execute(): array
    {
        $drive = new Drive($this->authService->getAuthenticatedClient());
        $rootId = config('filesystems.disks.google.folderId');

        $files = $this->fetchAllFiles($drive);

        $nodes = [];
        foreach ($files as $file) {
            $nodes[$file->id] = $this->mapToDTO($file);
        }

        $root = null;

        foreach ($files as $file) {
            $node = $nodes[$file->id];
            $parentId = $file->parents[0] ?? null;

            if ($file->id === $rootId) {
                $root = $node;

                continue;
            }

            if ($parentId && isset($nodes[$parentId])) {
                $nodes[$parentId]->addChild($node);
            }
        }

        return $root->toArray() ?? throw new \Exception('Root folder not found');
    }

    private function fetchAllFiles(Drive $drive): array
    {
        $files = [];
        $pageToken = null;

        do {
            $response = $drive->files->listFiles([
                'q' => 'trashed = false',
                'fields' => 'nextPageToken, files(id, name, mimeType, parents, webViewLink, createdTime)',
                'pageSize' => 1000,
                'pageToken' => $pageToken,
            ]);

            $files = array_merge($files, $response->files);
            $pageToken = $response->nextPageToken;
        } while ($pageToken);

        return $files;
    }

    private function mapToDTO(object $file): DriveFileDto
    {
        return str_contains($file->mimeType, 'folder')
            ? DriveFileDto::folder($file)
            : DriveFileDto::file($file);
    }
}

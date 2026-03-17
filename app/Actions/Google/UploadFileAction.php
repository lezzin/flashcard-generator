<?php

namespace App\Actions\Google;

use App\Services\Google\GoogleAuthService;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Http\UploadedFile;

class UploadFileAction
{
    public function __construct(
        private readonly GoogleAuthService $authService
    ) {}

    public function execute(UploadedFile $file): string
    {
        $client = $this->authService->getAuthenticatedClient();

        $driveService = new Drive($client);

        $fileMetadata = new DriveFile([
            'name' => $file->getClientOriginalName(),
            'parents' => [config('filesystems.disks.google.folderId')],
        ]);

        $content = file_get_contents($file->getRealPath());

        $file = $driveService->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => $file->getClientMimeType(),
            'uploadType' => 'multipart',
            'fields' => 'id',
        ]);

        return $file->id;
    }
}

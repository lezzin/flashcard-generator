<?php

namespace App\Actions\Google;

use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Illuminate\Http\UploadedFile;

class UploadFileAction
{
    public function execute(UploadedFile $file): string
    {
        $client = (new GetAuthenticatedGoogleClientAction())->execute();

        $service = new Google_Service_Drive($client);

        $driveFile = new Google_Service_Drive_DriveFile([
            'name' => $file->getClientOriginalName(),
            'parents' => [config('filesystems.disks.google.folderId')]
        ]);

        $result = $service->files->create($driveFile, [
            'data' => file_get_contents($file->getRealPath()),
            'mimeType' => $file->getMimeType(),
            'uploadType' => 'multipart',
        ]);

        return $result->id;
    }
}

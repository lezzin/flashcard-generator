<?php

namespace App\Actions\Anki;

use App\Actions\Anki\Api\ExportPackageAction;
use App\Actions\Google\UploadFileAction;
use App\Helpers\File;

class ExportPackageToDriveAction
{
    public function execute(string $deckName): void
    {
        try {
            $windowsLocalFilePath = File::buildWindowsPath($deckName);
            $linuxLocalFilePath = app(ExportPackageAction::class)->execute($deckName, $windowsLocalFilePath);

            $uploadedFile = File::wrapInUploadedFile($linuxLocalFilePath);
            app(UploadFileAction::class)->execute($uploadedFile, $deckName);
        } finally {
            File::deleteIfExists($linuxLocalFilePath);
        }
    }
}

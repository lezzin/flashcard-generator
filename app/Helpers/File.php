<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;

class File
{
    public static function convertWindowsToLinuxPath(string $path): string
    {
        $path = str_replace('\\', '/', $path);

        if (preg_match('/^([A-Za-z]):\/(.*)$/', $path, $matches)) {
            $drive = strtolower($matches[1]);
            $rest = $matches[2];

            return "/mnt/{$drive}/{$rest}";
        }

        return $path;
    }

    public static function deleteIfExists(string $filename)
    {
        if (file_exists($filename)) @unlink($filename);
    }

    public static function wrapInUploadedFile(string $path): UploadedFile
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

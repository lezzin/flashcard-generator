<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Str;

class File
{
    public static function buildWindowsPath(string $filename, string $extension = 'apkg', string $folder = 'Anki'): string
    {
        $safeName = Str::slug($filename, '_');
        $safeExtension = ltrim(Str::lower($extension), '.');

        $uniqueSuffix = now()->format('Ymd_His') . '_' . Str::random(6);
        $basePath = "C:/" . trim($folder, '/\\');

        if (!FileFacade::exists($basePath)) {
            FileFacade::makeDirectory($basePath, 0755, true);
        }

        return $basePath . DIRECTORY_SEPARATOR . "{$safeName}_{$uniqueSuffix}.{$safeExtension}";
    }

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
        if (file_exists($filename)) {
            @unlink($filename);
        }
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

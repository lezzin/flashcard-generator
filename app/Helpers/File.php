<?php

namespace App\Helpers;

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
}

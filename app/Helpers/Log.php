<?php

namespace App\Helpers;

class Log
{
    public static function getJson(
        string $path = 'gemini-backup.log',
        string $tag = 'FLASHCARD GENERATED'
    ): ?string {
        $fullPath = storage_path("logs/{$path}");

        if (!is_file($fullPath)) {
            return null;
        }

        $lines = file($fullPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (!$lines) {
            return null;
        }

        $lastLine = end($lines);

        return self::removeLogPrefix($lastLine, $tag);
    }

    public static function removeLogPrefix(string $logLine, string $tag = 'FLASHCARD GENERATED'): string
    {
        return preg_replace(
            '/^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]\s+\w+\.\w+:\s+\[' . preg_quote($tag, '/') . '\]\s*/',
            '',
            $logLine
        ) ?? $logLine;
    }
}

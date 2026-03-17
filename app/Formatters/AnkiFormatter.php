<?php

namespace App\Formatters;

use Illuminate\Support\Str;

class AnkiFormatter
{
    public static function deckName(string $deckName): string
    {
        $parts = explode('::', $deckName);
        $lastIndex = count($parts) - 1;

        return collect($parts)->map(function ($part, $index) use ($lastIndex) {
            $part = trim($part);

            if ($index === $lastIndex) {
                return $part;
            }

            if (strlen($part) <= 2) {
                return strtoupper($part);
            }

            if (str_contains($part, ' ')) {
                return collect(explode(' ', $part))
                    ->map(fn($word) => Str::substr($word, 0, 1) . '.')
                    ->join(' ');
            }

            return $part;
        })->join(' > ');
    }

    public static function note(array $note): array
    {
        $note['fields'] = collect($note['fields'])
            ->mapWithKeys(fn($field, $name) => [$name => strip_tags($field['value'])])
            ->all();

        unset(
            $note['tags'],
            $note['profile'],
            $note['mod'],
            $note['cards'],
        );

        return $note;
    }
}

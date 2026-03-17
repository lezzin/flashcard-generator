<?php

namespace App\Actions\Anki;

use Illuminate\Support\Str;

class GetDeckNamesAction
{
    public function __construct(
        private readonly InvokeAction $invokeAction
    ) {}

    public function execute()
    {
        $deckNames = $this->invokeAction->execute('deckNames');

        return collect($deckNames)->map(function (string $deckName) {
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
        });
    }
}

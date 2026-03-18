<?php

namespace App\DTOs\Anki;

use Illuminate\Support\Str;

class DeckDto
{
    public function __construct(
        public readonly string $name
    ) {}

    public static function fromRequest(string $request): self
    {
        return new self(
            name: $request,
        );
    }

    public function toArray(): array
    {
        return [
            'raw' => $this->name,
            'formatted' => $this->formattedName(),
        ];
    }

    private function formattedName(): string
    {
        $parts = explode('::', $this->name);
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
                    ->map(fn ($word) => Str::substr($word, 0, 1).'.')
                    ->join(' ');
            }

            return $part;
        })->join(' > ');
    }
}

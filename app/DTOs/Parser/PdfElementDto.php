<?php

namespace App\DTOs\Parser;

class PdfElementDto
{
    public function __construct(
        public string $type,
        public int $id,
        public int $pageNumber,
        public array $boundingBox,
        public ?string $content,
        public ?string $font,
        public ?float $fontSize,
        public ?string $textColor,
        public array $extra = [],
        /** @var PdfElementDto[] */
        public array $children = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'],
            id: $data['id'] ?? 0,
            pageNumber: $data['page number'],
            boundingBox: $data['bounding box'] ?? [],
            content: $data['content'] ?? null,
            font: $data['font'] ?? null,
            fontSize: isset($data['font size']) ? (float) $data['font size'] : null,
            textColor: $data['text color'] ?? null,
            extra: self::extractExtra($data),
            children: array_map(
                fn($child) => self::fromArray($child),
                $data['kids'] ?? $data['list items'] ?? []
            ),
        );
    }

    private static function extractExtra(array $data): array
    {
        return collect($data)
            ->except([
                'type',
                'id',
                'page number',
                'bounding box',
                'content',
                'font',
                'font size',
                'text color',
                'kids',
                'list items',
            ])
            ->toArray();
    }
}

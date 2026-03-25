<?php

namespace App\DTOs\Parser;

class PdfDataDto
{
    public function __construct(
        public string $fileName,
        public int $numberOfPages,
        public ?string $author,
        public ?string $title,
        public ?string $creationDate,
        public ?string $modificationDate,
        /** @var PdfElementDto[] */
        public array $elements,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            fileName: $data['file name'],
            numberOfPages: $data['number of pages'],
            author: $data['author'],
            title: $data['title'],
            creationDate: $data['creation date'],
            modificationDate: $data['modification date'],
            elements: array_map(
                fn ($item) => PdfElementDto::fromArray($item),
                $data['kids'] ?? []
            ),
        );
    }
}

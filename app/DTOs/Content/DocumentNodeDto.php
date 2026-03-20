<?php

namespace App\DTOs\Content;

class DocumentNodeDto
{
    public function __construct(
        public string $type,
        public ?string $title = null,
        public ?int $level = null,
        public ?string $content = null,
        public array $items = [],
        /** @var DocumentNodeDto[] */
        public array $children = [],
    ) {}

    public static function section(string $title, int $level): self
    {
        return new self(type: 'section', title: $title, level: $level);
    }

    public static function paragraph(string $content): self
    {
        return new self(type: 'paragraph', content: $content);
    }

    public static function list(array $items): self
    {
        return new self(type: 'list', items: $items);
    }
}

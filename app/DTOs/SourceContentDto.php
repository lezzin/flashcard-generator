<?php

namespace App\DTOs;

class SourceContentDto
{
    public function __construct(
        public readonly string $title,
        public readonly bool $isBackup = false,
        public readonly ?string $content = null,
    ) {}

    public static function fromAIResult(object $result)
    {
        return new self(
            title: $result->title,
            content: $result->content,
            isBackup: false
        );
    }
}

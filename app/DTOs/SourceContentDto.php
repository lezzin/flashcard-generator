<?php

namespace App\DTOs;

class SourceContentDto
{
    public function __construct(
        public readonly string $title,
        public readonly string $content,
    ) {
    }

    public static function fromAIResult(object $result)
    {
        return new self(
            title: $result->title,
            content: $result->content,
        );
    }
}

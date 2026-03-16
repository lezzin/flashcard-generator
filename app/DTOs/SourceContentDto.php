<?php

namespace App\DTOs;

class SourceContentDto
{
    public function __construct(
        public readonly string $title,
        public readonly string $content,
    ) {}
}

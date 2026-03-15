<?php

namespace App\DTOs;

class RawFlashcardDto
{
    public function __construct(
        public readonly string  $title,
        public readonly string $content,

    ) {}
}

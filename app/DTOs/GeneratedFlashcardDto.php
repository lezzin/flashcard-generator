<?php

namespace App\DTOs;

use App\Enums\CardTypes;

class GeneratedFlashcardDto
{
    public function __construct(
        public readonly CardTypes $type,
        public readonly string $front,
        public readonly string $title,
        public readonly ?string $back = null,
        public readonly ?string $extra = null,
    ) {}

    public static function omitFromObject(object $card)
    {
        return new self(
            type: CardTypes::CARD_OMIT,
            title: $card->title,
            front: $card->front,
            extra: $card?->extra ?? null,
        );
    }

    public static function simpleFromObject(object $card)
    {
        return new self(
            type: CardTypes::CARD_SIMPLE,
            title: $card->title,
            front: $card->front,
            back: $card?->back ?? null,
            extra: $card?->extra ?? null,
        );
    }

    public function toArray()
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'front' => $this->front,
            'back' => $this?->back ?? null,
            'extra' => $this?->extra ?? null,
        ];
    }
}

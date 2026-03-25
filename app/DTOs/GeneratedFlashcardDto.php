<?php

namespace App\DTOs;

use App\Enums\CardType;

class GeneratedFlashcardDto
{
    public function __construct(
        public readonly CardType $type,
        public readonly string $front,
        public readonly string $deck,
        public readonly ?string $back = null,
        public readonly ?string $extra = null,
    ) {
    }

    public static function omitFromObject(object $card)
    {
        return new self(
            type: CardType::CLOZE,
            deck: $card->deck,
            front: $card->front,
            extra: $card?->extra ?? null,
        );
    }

    public static function simpleFromObject(object $card)
    {
        return new self(
            type: CardType::SIMPLE,
            deck: $card->deck,
            front: $card->front,
            back: $card?->back ?? null,
            extra: $card?->extra ?? null,
        );
    }

    public function toArray()
    {
        return [
            'type' => $this->type,
            'deck' => $this->deck,
            'front' => $this->front,
            'back' => $this?->back ?? null,
            'extra' => $this?->extra ?? null,
        ];
    }
}

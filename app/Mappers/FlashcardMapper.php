<?php

namespace App\Mappers;

use App\DTOs\GeneratedFlashcardDto;
use App\Enums\CardType;

class FlashcardMapper
{
    public static function toDto(object $card, string $deckName): ?GeneratedFlashcardDto
    {
        $card->deck = $deckName;

        return match ($card->type) {
            CardType::CLOZE->value => GeneratedFlashcardDto::omitFromObject($card),
            CardType::SIMPLE->value => GeneratedFlashcardDto::simpleFromObject($card),
            default => null,
        };
    }
}

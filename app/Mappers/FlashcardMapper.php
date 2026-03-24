<?php

namespace App\Mappers;

use App\DTOs\GeneratedFlashcardDto;
use App\Enums\CardType;
use App\Models\AnkiFlashcard;

class FlashcardMapper
{
    public static function fromDatabaseToDto(AnkiFlashcard $card): GeneratedFlashcardDto
    {
        if ($card->type == CardType::CLOZE) {
            return new GeneratedFlashcardDto(
                type: CardType::CLOZE,
                deck: $card->deck,
                front: $card->fields['Texto'],
                extra: $card->fields['Extra'],
            );
        }

        return new GeneratedFlashcardDto(
            type: CardType::SIMPLE,
            deck: $card->deck,
            front: $card->fields['Frente'],
            back: $card->fields['Verso'],
            extra: $card->fields['Extra'],
        );
    }
}

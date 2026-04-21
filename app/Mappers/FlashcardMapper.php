<?php

namespace App\Mappers;

use App\DTOs\GeneratedFlashcardDto;
use App\Enums\CardType;

class FlashcardMapper
{
    public static function fromDatabaseToDto(object $card): GeneratedFlashcardDto
    {
        $fields = json_decode($card->fields);

        if ($card->type == CardType::CLOZE) {
            return new GeneratedFlashcardDto(
                type: CardType::CLOZE,
                deck: $card->deck,
                front: $fields->Texto,
                extra: $fields->Extra,
            );
        }

        return new GeneratedFlashcardDto(
            type: CardType::SIMPLE,
            deck: $card->deck,
            front: $fields->Frente,
            back: $fields->Verso,
            extra: $fields->Extra,
        );
    }
}

<?php

namespace App\Enums;

enum CardTypes: string
{
    case CARD_SIMPLE = 'MeF - Card simples';
    case CARD_OMIT = 'MeF - Omitir palavras';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

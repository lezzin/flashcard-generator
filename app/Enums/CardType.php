<?php

namespace App\Enums;

enum CardType: string
{
    case SIMPLE = 'MeF - Card simples';
    case CLOZE = 'MeF - Omitir palavras';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

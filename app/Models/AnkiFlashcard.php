<?php

namespace App\Models;

use App\Enums\CardType;
use Illuminate\Database\Eloquent\Model;

class AnkiFlashcard extends Model
{
    protected $fillable = [
        'type',
        'fields',
        'deck',
        'is_inserted',
    ];

    protected $casts = [
        'type'        => CardType::class,
        'fields'      => 'json',
        'is_inserted' => 'boolean',
    ];
}

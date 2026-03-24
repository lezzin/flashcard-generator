<?php

namespace App\Models;

use App\Enums\CardType;
use Illuminate\Database\Eloquent\Model;

class AnkiNote extends Model
{
    protected $fillable = [
        'anki_id',
        'fields_hash',
        'model_name',
        'type',
        'fields',
        'improved_fields',
        'keywords',
        'is_valid',
        'is_recoverable',
        'invalidation_reason',
    ];

    protected $casts = [
        'type'   => CardType::class,
        'fields' => 'json',
        'improved_fields' => 'json',
        'keywords' => 'json',
        'is_valid' => 'boolean',
        'is_recoverable' => 'boolean',
    ];
}

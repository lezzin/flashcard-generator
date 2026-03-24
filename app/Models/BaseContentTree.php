<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseContentTree extends Model
{
    protected $fillable = [
        'data',
        'is_inserted',
    ];

    protected $casts = [
        'data'        => 'json',
        'is_inserted' => 'boolean',
    ];
}

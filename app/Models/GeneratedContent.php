<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneratedContent extends Model
{
    protected $fillable = [
        "description",
        "title",
        "tree_id"
    ];

    public function tree()
    {
        return $this->belongsTo(BaseContentTree::class, 'tree_id');
    }
}

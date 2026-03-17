<?php

namespace App\Http\Requests\Flashcard;

use Illuminate\Foundation\Http\FormRequest;

class ImproveFlashcardsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'deck_name' => ['required', 'string'],
        ];
    }
}

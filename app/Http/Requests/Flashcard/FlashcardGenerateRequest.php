<?php

namespace App\Http\Requests\Flashcard;

use Illuminate\Foundation\Http\FormRequest;

class FlashcardGenerateRequest extends FormRequest
{
    public function rules(): array
    {
        return  [
            'content' => ['required', 'string'],
        ];
    }
}

<?php

namespace App\Http\Requests\Flashcard;

use Illuminate\Foundation\Http\FormRequest;

class FlashcardGenerateByBackupRequest extends FormRequest
{
    public function rules(): array
    {
        return  [
            'title'   => ['required', 'string'],
        ];
    }
}

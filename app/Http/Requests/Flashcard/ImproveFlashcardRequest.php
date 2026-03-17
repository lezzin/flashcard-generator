<?php

namespace App\Http\Requests\Flashcard;

use Illuminate\Foundation\Http\FormRequest;

class ImproveFlashcardRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'note_id' => ['required', 'string'],
        ];
    }
}

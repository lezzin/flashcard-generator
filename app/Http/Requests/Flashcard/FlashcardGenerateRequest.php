<?php

namespace App\Http\Requests\Flashcard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FlashcardGenerateRequest extends FormRequest
{
    public function rules(): array
    {
        return  [
            'title' => ['required', 'string'],
            'tree_id' => ['required', 'string', Rule::exists('generated_contents', 'id')],
        ];
    }
}

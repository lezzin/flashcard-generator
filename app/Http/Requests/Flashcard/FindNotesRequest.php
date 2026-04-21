<?php

namespace App\Http\Requests\Flashcard;

use Illuminate\Foundation\Http\FormRequest;

class FindNotesRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'deck_name' => ['nullable', 'string'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'strip_tags' => ['nullable', 'boolean'],
            'filter_by_style' => ['nullable', 'boolean'],
        ];
    }
}

<?php

namespace App\Http\Requests\Flashcard;

use Illuminate\Foundation\Http\FormRequest;

class ExportPackageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'deck_name' => ['nullable', 'string'],
        ];
    }
}

<?php

namespace App\Http\Requests\Database;

use Illuminate\Foundation\Http\FormRequest;

class GetNotesRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'per_page' => ['nullable', 'integer'],
            'page'     => ['required', 'integer'],
        ];
    }
}

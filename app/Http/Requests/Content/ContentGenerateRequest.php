<?php

namespace App\Http\Requests\Content;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class ContentGenerateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'file' => ['required', File::types(['pdf'])],
        ];
    }
}

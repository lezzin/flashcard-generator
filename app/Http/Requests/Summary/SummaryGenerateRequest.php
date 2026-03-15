<?php

namespace App\Http\Requests\Summary;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class SummaryGenerateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'file' => ['required', File::types(['pdf', 'txt'])]
        ];
    }
}

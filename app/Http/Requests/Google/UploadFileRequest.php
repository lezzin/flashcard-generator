<?php

namespace App\Http\Requests\Google;

use Illuminate\Foundation\Http\FormRequest;

class UploadFileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'file' => ['required', 'file']
        ];
    }
}

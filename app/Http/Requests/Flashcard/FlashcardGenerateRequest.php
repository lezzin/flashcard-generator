<?php

namespace App\Http\Requests\Flashcard;

use Illuminate\Foundation\Http\FormRequest;

class FlashcardGenerateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'content' => ['required'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $content = $this->input('content');

            if ($this->hasFile('content')) {
                $file = $this->file('content');

                if ($file->getClientOriginalExtension() !== 'json') {
                    $validator->errors()->add('content', 'The file must be a JSON.');
                }

                return;
            }

            if (!is_string($content)) {
                $validator->errors()->add('content', 'Content must be a string or a JSON file.');
            }
        });
    }
}

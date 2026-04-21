<?php

namespace App\Http\Controllers;

use App\Actions\Anki\GenerateContentAction;
use App\Http\Requests\Flashcard\FlashcardGenerateRequest;

class FlashcardController extends Controller
{
    public function store(FlashcardGenerateRequest $request, GenerateContentAction $action)
    {
        $action->execute($request->input('content'));

        return response()->noContent();
    }
}

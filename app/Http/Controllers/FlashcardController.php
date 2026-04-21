<?php

namespace App\Http\Controllers;

use App\Http\Requests\Flashcard\FlashcardGenerateRequest;
use App\Jobs\Flashcard\GenerateFlashcardJob;

class FlashcardController extends Controller
{
    public function store(FlashcardGenerateRequest $request)
    {
        dispatch(
            new GenerateFlashcardJob($request->input('content'))
        )->onQueue('flashcard:generate');

        return response()->noContent();
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\Flashcard\FlashcardGenerateRequest;
use App\Jobs\GenerateFlashcardsJob;
use App\Services\Anki\AnkiConnectClient;

class FlashcardController extends Controller
{
    public function store(FlashcardGenerateRequest $request, AnkiConnectClient $anki)
    {
        $anki->validateConnection();

        $content = $request->input('content');
        $isPath = false;

        if ($request->hasFile('content')) {
            $content = $request->file('content')->store('uploads');
            $isPath = true;
        }

        dispatch(new GenerateFlashcardsJob(
            title: $request->input('title'),
            content: $content,
            isPath: $isPath,
        ))->onQueue('flashcard:generate');

        return response()->noContent();
    }
}

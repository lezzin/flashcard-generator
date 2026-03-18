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

        dispatch(new GenerateFlashcardsJob(
            content: $request->post('content'),
            title: $request->post('title'),
        ))->onQueue('flashcard:generate');

        return response()->noContent();
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\Flashcard\FlashcardGenerateRequest;
use App\Jobs\Flashcard\FlashcardPipelineJob;
use App\Services\Anki\AnkiConnectClient;

class FlashcardController extends Controller
{
    public function store(FlashcardGenerateRequest $request, AnkiConnectClient $anki)
    {
        $anki->validateConnection();

        dispatch(new FlashcardPipelineJob(
            title: $request->input('title'),
            treeId: $request->input('tree_id'),
        ))->onQueue('flashcard:generate');

        return response()->noContent();
    }
}

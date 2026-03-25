<?php

namespace App\Http\Controllers;

use App\Http\Requests\Flashcard\AddToAnkiRequest;
use App\Http\Requests\Flashcard\FlashcardGenerateRequest;
use App\Jobs\Flashcard\AddFlashcardToAnkiJob;
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

    public function addToAnki(AddToAnkiRequest $request)
    {
        dispatch(
            new AddFlashcardToAnkiJob($request->input('tree_id'))
        )->onQueue('flashcard:add');

        return response()->noContent();
    }
}

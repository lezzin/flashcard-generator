<?php

namespace App\Http\Controllers;

use App\Http\Requests\Flashcard\FlashcardGenerateRequest;
use App\Pipelines\Flashcard\FlashcardPipeline;
use App\Pipelines\Flashcard\FlashcardReprocessPipeline;

class FlashcardController extends Controller
{
    public function generate(FlashcardGenerateRequest $request)
    {
        FlashcardPipeline::handle($request->post('content'));
        return response()->noContent();
    }

    public function reprocess(FlashcardGenerateRequest $request)
    {
        FlashcardReprocessPipeline::handle($request->post('content'));
        return response()->noContent();
    }
}

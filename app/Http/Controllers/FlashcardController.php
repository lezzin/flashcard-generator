<?php

namespace App\Http\Controllers;

use App\Actions\Anki\ValidateConnectionAction;
use App\Http\Requests\Flashcard\FlashcardGenerateRequest;
use App\Pipelines\Flashcard\FlashcardPipeline;
use App\Pipelines\Flashcard\FlashcardReprocessPipeline;

class FlashcardController extends Controller
{
    public function generate(FlashcardGenerateRequest $request)
    {
        app(ValidateConnectionAction::class)->execute();
        FlashcardPipeline::handle($request->post('content'), $request->post('title'));

        return response()->noContent();
    }

    public function reprocess(FlashcardGenerateRequest $request)
    {
        app(ValidateConnectionAction::class)->execute();
        FlashcardReprocessPipeline::handle($request->post('content'), $request->post('title'));

        return response()->noContent();
    }
}

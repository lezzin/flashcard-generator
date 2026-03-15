<?php

namespace App\Http\Controllers;

use App\Http\Requests\Flashcard\FlashcardGenerateRequest;
use App\Pipelines\Flashcard\FlashcardPipeline;

class FlashcardController extends Controller
{
    public function generate(FlashcardGenerateRequest $request)
    {
        return FlashcardPipeline::handle($request->post('content'));
    }
}

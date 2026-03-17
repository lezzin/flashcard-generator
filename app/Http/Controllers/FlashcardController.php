<?php

namespace App\Http\Controllers;

use App\Actions\Anki\FindNotesByDeckNameAction;
use App\Actions\Anki\GetDeckNamesAction;
use App\Actions\Anki\ValidateConnectionAction;
use App\Actions\Flashcard\ImproveFlashcardAction;
use App\Actions\Flashcard\ImproveFlashcardsAction;
use App\Http\Requests\Flashcard\FindNotesRequest;
use App\Http\Requests\Flashcard\FlashcardGenerateRequest;
use App\Http\Requests\Flashcard\ImproveFlashcardRequest;
use App\Http\Requests\Flashcard\ImproveFlashcardsRequest;
use App\Jobs\GenerateFlashcardsJob;
use App\Pipelines\Flashcard\FlashcardReprocessPipeline;

class FlashcardController extends Controller
{
    public function generate(FlashcardGenerateRequest $request, ValidateConnectionAction $action)
    {
        $action->execute();

        dispatch(new GenerateFlashcardsJob(
            content: $request->post('content'),
            title: $request->post('title'),
        ))->onQueue('flashcard:generate');

        return response()->noContent();
    }

    public function reprocess(FlashcardGenerateRequest $request, ValidateConnectionAction $action)
    {
        $action->execute();

        FlashcardReprocessPipeline::handle(
            $request->post('content'),
            $request->post('title')
        );

        return response()->noContent();
    }

    public function improveMany(ImproveFlashcardsRequest $request, ImproveFlashcardsAction $manyAction)
    {
        return $manyAction->execute($request->post('deck_name'));
    }

    public function improveSingle(ImproveFlashcardRequest $request, ImproveFlashcardAction $singleAction)
    {
        return $singleAction->execute($request->post('note_id'));
    }

    public function getDeckNames(GetDeckNamesAction $action)
    {
        return $action->execute();
    }

    public function findNotes(FindNotesRequest $request, FindNotesByDeckNameAction $action)
    {
        return $action->execute(
            $request->input('deck_name'),
            $request->input('per_page', 100),
            $request->input('page'),
        );
    }
}

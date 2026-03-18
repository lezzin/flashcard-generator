<?php

namespace App\Http\Controllers;

use App\Actions\Anki\ExportPackageAction;
use App\Actions\Anki\FindNotesByDeckNameAction;
use App\Actions\Anki\GetDeckNamesAction;
use App\Actions\Flashcard\Optimize\OptimizeDeckAction;
use App\Actions\Flashcard\Optimize\OptimizeNoteAction;
use App\Http\Requests\Flashcard\ExportPackageRequest;
use App\Http\Requests\Flashcard\FindNotesRequest;
use App\Http\Requests\Flashcard\FlashcardGenerateRequest;
use App\Http\Requests\Flashcard\ImproveFlashcardRequest;
use App\Http\Requests\Flashcard\ImproveFlashcardsRequest;
use App\Jobs\GenerateFlashcardsJob;
use App\Pipelines\Flashcard\FlashcardReprocessPipeline;
use App\Services\Anki\AnkiConnectClient;

class FlashcardController extends Controller
{
    public function generate(FlashcardGenerateRequest $request, AnkiConnectClient $anki)
    {
        $anki->validateConnection();

        dispatch(new GenerateFlashcardsJob(
            content: $request->post('content'),
            title: $request->post('title'),
        ))->onQueue('flashcard:generate');

        return response()->noContent();
    }

    public function reprocess(FlashcardGenerateRequest $request, AnkiConnectClient $anki)
    {
        $anki->validateConnection();

        FlashcardReprocessPipeline::handle(
            $request->post('content'),
            $request->post('title')
        );

        return response()->noContent();
    }

    public function improveMany(ImproveFlashcardsRequest $request, OptimizeDeckAction $action)
    {
        return $action->execute($request->post('deck_name'));
    }

    public function improveSingle(ImproveFlashcardRequest $request, OptimizeNoteAction $action)
    {
        return $action->execute($request->post('note_id'));
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

    public function exportDeck(ExportPackageRequest $request, ExportPackageAction $action)
    {
        $result = $action->execute($request->post('deck_name', null));

        return response()->json([
            'message' => 'Exported successfully!',
            ...$result,
        ]);
    }
}

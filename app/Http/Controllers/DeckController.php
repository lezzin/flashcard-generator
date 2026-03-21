<?php

namespace App\Http\Controllers;

use App\Actions\Anki\GenerateFromDeckAction;
use App\Actions\Anki\GetDeckNamesAction;
use App\Http\Requests\Flashcard\ExportPackageRequest;
use App\Http\Requests\Flashcard\GenerateFromDeckRequest;
use App\Http\Requests\Flashcard\ImproveFlashcardsRequest;
use App\Jobs\Deck\ExportPackageJob;
use App\Jobs\Deck\ImproveDeckJob;
use App\Services\Anki\AnkiConnectClient;

class DeckController extends Controller
{
    public function index(GetDeckNamesAction $action)
    {
        return $action->execute();
    }

    public function export(ExportPackageRequest $request, AnkiConnectClient $anki)
    {
        $anki->validateConnection();

        dispatch(new ExportPackageJob(
            deckName: $request->post('deck_name', null),
        ))->onQueue('deck:export');

        return response()->noContent();
    }

    public function improve(ImproveFlashcardsRequest $request, AnkiConnectClient $anki)
    {
        $anki->validateConnection();

        dispatch(new ImproveDeckJob(
            deckName: $request->input('deck_name'),
        ))->onQueue('deck:improve');

        return response()->noContent();
    }

    public function generate(GenerateFromDeckRequest $request, GenerateFromDeckAction $action)
    {
        $action->execute($request->input('deck_name'));

        return response()->noContent();
    }
}

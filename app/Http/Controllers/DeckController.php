<?php

namespace App\Http\Controllers;

use App\Actions\Anki\Export\DispatchExportPackageAction;
use App\Actions\Anki\Generation\GenerateFromDeckAction;
use App\Actions\Anki\Decks\GetDeckNamesAction;
use App\Actions\Anki\Optimization\DispatchOptimizeDeckAction;
use App\Http\Requests\Flashcard\ExportPackageRequest;
use App\Http\Requests\Flashcard\GenerateFromDeckRequest;
use App\Http\Requests\Flashcard\ImproveFlashcardsRequest;
use App\Services\Anki\AnkiConnectClient;

class DeckController extends Controller
{
    public function index(GetDeckNamesAction $action)
    {
        return $action->execute();
    }

    public function export(ExportPackageRequest $request, AnkiConnectClient $anki, DispatchExportPackageAction $action)
    {
        $anki->validateConnection();

        $action->execute($request->input('deck_name'));

        return response()->noContent();
    }

    public function improve(ImproveFlashcardsRequest $request, AnkiConnectClient $anki, DispatchOptimizeDeckAction $action)
    {
        $anki->validateConnection();

        $action->execute($request->input('deck_name'));

        return response()->noContent();
    }

    public function generate(GenerateFromDeckRequest $request, GenerateFromDeckAction $action)
    {
        $action->execute($request->input('deck_name'));

        return response()->noContent();
    }
}

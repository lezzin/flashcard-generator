<?php

namespace App\Http\Controllers;

use App\Actions\Anki\Api\GetDeckNamesAction;
use App\Actions\Anki\DispatchExportPackageToDriveAction;
use App\Actions\Anki\GenerateFlashcardFromDeckAction;
use App\Actions\Anki\DispatchOptimizeDeckAction;
use App\Http\Requests\Flashcard\ExportPackageRequest;
use App\Http\Requests\Flashcard\GenerateFromDeckRequest;
use App\Http\Requests\Flashcard\ImproveFlashcardsRequest;
use App\Http\Resources\ResponseResource;
use App\Services\Anki\AnkiConnectClient;

class DeckController extends Controller
{
    public function index(GetDeckNamesAction $action)
    {
        return $action->execute();
    }

    public function export(ExportPackageRequest $request, AnkiConnectClient $anki, DispatchExportPackageToDriveAction $action)
    {
        $anki->validateConnection();

        $action->execute($request->input('deck_name'));

        return response()->noContent();
    }

    public function improve(ImproveFlashcardsRequest $request, AnkiConnectClient $anki, DispatchOptimizeDeckAction $action)
    {
        $anki->validateConnection();

        $result = $action->execute(
            deckName: $request->input('deck_name'),
            filterByStyle: $request->input('filter_by_style', false)
        );

        return response()->json([
            'message' => 'Enviado para a fila!',
            'data'    => $result
        ]);
    }

    public function generate(GenerateFromDeckRequest $request, GenerateFlashcardFromDeckAction $action)
    {
        $action->execute($request->input('deck_name'));

        return response()->noContent();
    }
}

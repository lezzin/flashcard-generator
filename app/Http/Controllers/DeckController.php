<?php

namespace App\Http\Controllers;

use App\Actions\Anki\ExportPackageAction;
use App\Actions\Anki\GetDeckNamesAction;
use App\Actions\Flashcard\Optimize\OptimizeDeckAction;
use App\Http\Requests\Flashcard\ExportPackageRequest;
use App\Http\Requests\Flashcard\ImproveFlashcardsRequest;

class DeckController extends Controller
{
    public function index(GetDeckNamesAction $action)
    {
        return $action->execute();
    }

    public function export(ExportPackageRequest $request, ExportPackageAction $action)
    {
        $result = $action->execute($request->post('deck_name', null));

        return response()->json([
            'message' => 'Exported successfully!',
            ...$result,
        ]);
    }

    public function improve(ImproveFlashcardsRequest $request, OptimizeDeckAction $action)
    {
        return $action->execute($request->post('deck_name'));
    }
}

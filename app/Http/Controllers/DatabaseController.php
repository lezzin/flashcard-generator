<?php

namespace App\Http\Controllers;

use App\Actions\Anki\GetGeneratedContentsAction;
use App\Actions\Anki\GetNotesAction;
use App\Http\Requests\Database\PaginatedRequest;
use App\Http\Resources\Database\GetNoteResource;

class DatabaseController extends Controller
{
    public function getNotes(PaginatedRequest $request, GetNotesAction $action)
    {
        $items = $action->execute(
            $request->input('page'),
            $request->input('per_page', 50),
        );

        return GetNoteResource::collection($items);
    }

    public function getGeneratedContents(PaginatedRequest $request, GetGeneratedContentsAction $action)
    {
        $items = $action->execute(
            $request->input('page'),
            $request->input('per_page', 50),
        );

        return $items;
    }
}

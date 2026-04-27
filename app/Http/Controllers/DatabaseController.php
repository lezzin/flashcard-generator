<?php

namespace App\Http\Controllers;

use App\Actions\Anki\GetDatabaseNotesAction;
use App\Http\Requests\Database\PaginatedRequest;
use App\Http\Resources\Database\GetNoteResource;

class DatabaseController extends Controller
{
    public function getNotes(PaginatedRequest $request, GetDatabaseNotesAction $action)
    {
        $items = $action->execute(
            $request->input('page'),
            $request->input('per_page', 50),
        );

        return GetNoteResource::collection($items);
    }
}

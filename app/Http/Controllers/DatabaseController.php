<?php

namespace App\Http\Controllers;

use App\Actions\Anki\Database\GetNotesAction;
use App\Http\Requests\Database\GetNotesRequest;
use App\Http\Resources\Database\GetNoteResource;

class DatabaseController extends Controller
{
    public function getNotes(GetNotesRequest $request, GetNotesAction $action)
    {
        $items = $action->execute(
            $request->input('page'),
            $request->input('per_page', 50),
        );

        return GetNoteResource::collection($items);
    }
}

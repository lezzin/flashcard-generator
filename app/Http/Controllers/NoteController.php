<?php

namespace App\Http\Controllers;

use App\Actions\Anki\FindNotesByDeckNameAction;
use App\Actions\Flashcard\Optimize\OptimizeNoteAction;
use App\Http\Requests\Flashcard\FindNotesRequest;
use App\Http\Requests\Flashcard\ImproveFlashcardRequest;

class NoteController extends Controller
{
    public function index(FindNotesRequest $request, FindNotesByDeckNameAction $action)
    {
        return $action->execute(
            $request->input('deck_name'),
            $request->input('per_page', 100),
            $request->input('page'),
        );
    }

    public function improve(ImproveFlashcardRequest $request, OptimizeNoteAction $action)
    {
        return $action->execute($request->post('note_id'));
    }
}

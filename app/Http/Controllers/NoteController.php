<?php

namespace App\Http\Controllers;

use App\Actions\Anki\Api\FindNotesByDeckNameAction;
use App\Actions\Anki\OptimizeNoteAction;
use App\Http\Requests\Flashcard\FindNotesRequest;
use App\Http\Requests\Flashcard\ImproveFlashcardRequest;

class NoteController extends Controller
{
    public function index(FindNotesRequest $request, FindNotesByDeckNameAction $action)
    {
        return $action->execute(
            deckName: $request->input('deck_name'),
            perPage: $request->input('per_page', 100),
            page: $request->input('page'),
            stripTags: $request->input('strip_tags', true),
            filterByStyle: $request->input('filter_by_style', false),
        );
    }

    public function improve(ImproveFlashcardRequest $request, OptimizeNoteAction $action)
    {
        return $action->execute($request->post('note_id'));
    }
}

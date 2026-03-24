<?php

namespace App\Actions\Anki;

use App\Models\AnkiNote;

class GetNotesAction
{
    public function execute(int $page, ?int $perPage = 50)
    {
        return AnkiNote::paginate(
            perPage: $perPage,
            page: $page
        );
    }
}

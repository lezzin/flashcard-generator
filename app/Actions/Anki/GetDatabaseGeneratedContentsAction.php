<?php

namespace App\Actions\Anki;

use App\Models\GeneratedContent;

class GetDatabaseGeneratedContentsAction
{
    public function execute(int $page, ?int $perPage = 50)
    {
        return GeneratedContent::paginate(
            perPage: $perPage,
            page: $page
        );
    }
}

<?php

namespace App\Actions\Anki\Database;

use App\Models\GeneratedContent;

class GetGeneratedContentsAction
{
    public function execute(int $page, ?int $perPage = 50)
    {
        return GeneratedContent::paginate(
            perPage: $perPage,
            page: $page
        );
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\Content\ContentGenerateRequest;
use App\Pipelines\Content\ContentPipeline;
use App\Services\Anki\AnkiConnectClient;

class ContentController extends Controller
{
    public function store(ContentGenerateRequest $request, AnkiConnectClient $anki)
    {
        $anki->validateConnection();

        ContentPipeline::handle($request->file('file'));

        return response()->noContent();
    }
}

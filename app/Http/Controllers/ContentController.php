<?php

namespace App\Http\Controllers;

use App\Http\Requests\Content\ContentGenerateRequest;
use App\Jobs\Content\ContentPipelineJob;
use Illuminate\Support\Facades\Storage;

class ContentController extends Controller
{
    public function store(ContentGenerateRequest $request)
    {
        $file = $request->file('file');
        $uploaded = $file->move(Storage::path('output'), 'temp.pdf');

        dispatch(new ContentPipelineJob(
            filePath: $uploaded->getRealPath(),
        ))->onQueue('content:generate');

        return response()->noContent();
    }
}

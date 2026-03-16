<?php

namespace App\Http\Controllers;

use App\Actions\Anki\ValidateConnectionAction;
use App\Http\Requests\Summary\SummaryGenerateRequest;
use App\Pipelines\Summary\SummaryPipeline;

class SummaryController extends Controller
{
    public function generate(SummaryGenerateRequest $request)
    {
        app(ValidateConnectionAction::class)->execute();
        SummaryPipeline::handle($request->file('file'));

        return response()->noContent();
    }
}

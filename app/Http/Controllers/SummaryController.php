<?php

namespace App\Http\Controllers;

use App\Http\Requests\Summary\SummaryGenerateRequest;
use App\Pipelines\Summary\SummaryPipeline;

class SummaryController extends Controller
{
    public function generate(SummaryGenerateRequest $request)
    {
        return SummaryPipeline::handle($request->file('file'));
    }
}

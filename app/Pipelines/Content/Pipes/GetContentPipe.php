<?php

namespace App\Pipelines\Content\Pipes;

use App\Actions\PdfParser\ParsePdfAction;
use App\Pipelines\Content\ContentPipelineContext;
use Closure;
use Exception;

class GetContentPipe
{
    public function __construct(
        private readonly ParsePdfAction $parsePdfAction
    ) {}

    public function handle(ContentPipelineContext $context, Closure $next)
    {
        try {
            $context->pdf = $this->parsePdfAction->execute($context->filePath);
        } catch (Exception $e) {
            throw new Exception('Error parsing PDF: ' . $e->getMessage());
        }

        return $next($context);
    }
}

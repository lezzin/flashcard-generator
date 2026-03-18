<?php

namespace App\Pipelines\Content\Pipes;

use App\Pipelines\Content\ContentPipelineContext;
use Closure;
use Exception;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser;

class GetContentPipe
{
    public function handle(ContentPipelineContext $context, Closure $next)
    {
        $parser = new Parser;

        try {
            $pdf = $parser->parseFile($context->file->path());

            $fullText = '';
            foreach ($pdf->getPages() as $page) {
                $fullText .= $page->getText() . "\n";
            }

            $context->content = $this->cleanExtractedText($fullText);
        } catch (Exception $e) {
            throw new Exception('Error parsing PDF: ' . $e->getMessage());
        }

        return $next($context);
    }

    private function cleanExtractedText(string $text): string
    {
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');

        $text = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $text);
        $text = preg_replace('/\p{C}+/u', '', $text);

        $text = str_replace(
            ['●', '▪', '◦', '–', '—'],
            ['•', '•', '•', '-', '-'],
            $text
        );

        $text = preg_replace('/ ?• ?/', "\n• ", $text);

        $text = preg_replace('/[ \t]+/', ' ', $text);

        $text = preg_replace("/\n +/", "\n", $text);

        $text = preg_replace("/\n{3,}/", "\n\n", $text);

        $text = Str::of($text)
            ->trim()
            ->replaceMatches('/ +\n/', "\n")
            ->toString();

        if (blank($text)) {
            throw new Exception('No text could be extracted from the PDF');
        }

        return $text;
    }
}

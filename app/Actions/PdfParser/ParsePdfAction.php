<?php

namespace App\Actions\PdfParser;

use App\DTOs\Parser\PdfDataDto;
use Exception;
use Illuminate\Support\Facades\Http;

class ParsePdfAction
{
    public function execute(string $filePath): PdfDataDto
    {
        $url = config('services.parser.host');

        $response = Http::timeout(120)->get("{$url}/parse", [
            'file' => $filePath
        ]);

        if (!$response->json()['data']) {
            throw new Exception("Failed to get parsed PDF content");
        }

        return PdfDataDto::fromArray($response->json('data'));
    }
}

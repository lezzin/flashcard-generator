<?php

namespace App\Services\Parser;

use App\DTOs\Parser\PdfDataDto;
use App\Exceptions\Parser\ParserConnectionException;
use App\Exceptions\Parser\ParserEmptyDataException;
use App\Exceptions\Parser\ParserInvalidResponseException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class ParserService
{
    protected string $url;
    protected string $timeout;

    public function __construct()
    {
        $config = config('services.parser');

        $this->url = $config['host'];
        $this->timeout = $config['timeout'];
    }

    public function handle(string $filePath)
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get($this->urlPath('/parse'), [
                    'file' => $filePath
                ]);
        } catch (ConnectionException) {
            throw new ParserConnectionException();
        }

        if (! $response->ok()) {
            throw new ParserConnectionException();
        }

        $json = $response->json();

        if (! is_array($json)) {
            throw new ParserInvalidResponseException();
        }

        if (empty($json['data'])) {
            throw new ParserEmptyDataException();
        }

        return PdfDataDto::fromArray($json['data']);
    }

    private function urlPath(string $path): string
    {
        return "{$this->url}/{$path}";
    }
}

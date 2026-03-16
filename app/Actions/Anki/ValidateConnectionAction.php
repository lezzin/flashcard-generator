<?php

namespace App\Actions\Anki;

use Exception;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ValidateConnectionAction
{
    protected string $url;

    public function __construct()
    {
        $this->url = config('services.anki.host');
    }

    public function execute(): void
    {
        try {
            $this->validateConnection();
        } catch (Throwable $e) {
            throw new HttpException(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                'Erro ao validar conexão com o Anki.',
                $e
            );
        }
    }

    private function validateConnection()
    {
        $response = Http::timeout(10)->get($this->url);
        if ($response->ok()) return;
        throw new Exception('Failed to connect to AnkiConnect.');
    }
}

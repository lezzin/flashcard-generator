<?php

namespace App\Services\Anki;

use Exception;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class AnkiConnectClient
{
    protected string $url;

    public function __construct()
    {
        $this->url = config('services.anki.host');
    }

    public function invoke(string $action, array $params = []): mixed
    {
        $requestJson = $this->getRequestPayload($action, $params);
        $response = Http::timeout(60)->post($this->url, $requestJson);

        if (! $response->ok()) {
            throw new Exception('Failed to connect to AnkiConnect.');
        }

        $data = $response->json();

        if (! is_null($data['error'])) {
            throw new Exception($data['error']);
        }

        return $data['result'];
    }

    public function validateConnection(): void
    {
        try {
            $response = Http::timeout(10)->get($this->url);
            if (! $response->ok()) {
                throw new Exception('Failed to connect to AnkiConnect.');
            }
        } catch (Throwable $e) {
            throw new HttpException(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                'Erro ao validar conexão com o Anki. Certifique-se que o Anki está aberto e o AnkiConnect instalado.',
                $e
            );
        }
    }

    private function getRequestPayload(string $action, array $params = []): array
    {
        $version = 6;

        if ($action == 'deckNames') {
            return [
                'action' => $action,
                'version' => $version,
            ];
        }

        return [
            'action' => $action,
            'params' => $params,
            'version' => $version,
        ];
    }
}

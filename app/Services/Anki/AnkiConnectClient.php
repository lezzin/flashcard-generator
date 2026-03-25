<?php

namespace App\Services\Anki;

use App\Exceptions\Anki\AnkiConnectionException;
use App\Exceptions\Anki\AnkiResponseException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class AnkiConnectClient
{
    protected string $url;
    protected string $timeout;

    public function __construct()
    {
        $config = config('services.anki');

        $this->url = $config['host'];
        $this->timeout = $config['timeout'];
    }

    public function invoke(string $action, array $params = []): mixed
    {
        try {
            $response = Http::timeout($this->timeout)->post(
                $this->url,
                $this->getRequestPayload($action, $params)
            );
        } catch (ConnectionException $e) {
            throw new AnkiConnectionException();
        }

        if (! $response->ok()) {
            throw new AnkiConnectionException();
        }

        $data = $response->json();

        if (! is_null($data['error'])) {
            throw new AnkiResponseException($data['error']);
        }

        return $data['result'];
    }

    public function validateConnection(): void
    {
        try {
            $response = Http::timeout($this->timeout)->get($this->url);

            if (! $response->ok()) {
                throw new AnkiConnectionException();
            }
        } catch (ConnectionException) {
            throw new AnkiConnectionException();
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

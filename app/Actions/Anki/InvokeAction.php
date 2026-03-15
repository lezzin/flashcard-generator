<?php

namespace App\Actions\Anki;

use Illuminate\Support\Facades\Http;
use Exception;

class InvokeAction
{
    protected string $url;

    public function __construct()
    {
        $this->url = config('services.anki.host');
    }

    public function __invoke(string $action, array $params = []): mixed
    {
        $requestJson = $this->getRequestPayload($action, $params);
        $response = Http::post($this->url, $requestJson);

        if (!$response->ok()) {
            throw new Exception('Failed to connect to AnkiConnect.');
        }

        $data = $response->json();

        if (!is_null($data['error'])) {
            throw new Exception($data['error']);
        }

        return $data['result'];
    }

    private function getRequestPayload(string $action, array $params = []): array
    {
        $version = 6;

        if ($action == "deckNames") {
            return [
                'action' => $action,
                'version' => $version
            ];
        }

        return [
            'action' => $action,
            'params' => $params,
            'version' => $version
        ];
    }
}

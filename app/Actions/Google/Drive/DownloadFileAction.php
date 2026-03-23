<?php

namespace App\Actions\Google\Drive;

use App\Services\Google\GoogleAuthService;
use Google\Service\Drive;

class DownloadFileAction
{
    public function __construct(
        private readonly GoogleAuthService $authService
    ) {}

    public function execute(string $id): array
    {
        $client = $this->authService->getAuthenticatedClient();
        $drive = new Drive($client);

        $file = $drive->files->get($id, [
            'fields' => 'name, mimeType',
        ]);

        $httpClient = $client->authorize();

        $response = $httpClient->request('GET', "https://www.googleapis.com/drive/v3/files/{$id}", [
            'query' => ['alt' => 'media'],
            'stream' => true,
        ]);

        return [
            'file' => $file,
            'stream' => $response->getBody(),
        ];
    }
}

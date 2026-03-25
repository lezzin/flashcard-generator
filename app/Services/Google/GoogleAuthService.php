<?php

namespace App\Services\Google;

use Google_Client;
use Illuminate\Support\Facades\Storage;

class GoogleAuthService
{
    public function __construct(
        private readonly GoogleClientFactory $factory
    ) {
    }

    public function getAuthenticatedClient(): Google_Client
    {
        $client = $this->factory->create();

        $tokenPath = 'google/token.json';

        if (! Storage::disk('local')->exists($tokenPath)) {
            throw new \Exception('Google account not connected.');
        }

        $accessToken = json_decode(Storage::disk('local')->get($tokenPath), true);
        $client->setAccessToken($accessToken);

        if ($client->isAccessTokenExpired()) {
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                Storage::disk('local')->put($tokenPath, json_encode($client->getAccessToken()));
            } else {
                throw new \Exception('Google access token expired and no refresh token available.');
            }
        }

        return $client;
    }

    public function saveToken(array $token): void
    {
        Storage::disk('local')->put('google/token.json', json_encode($token));
    }
}

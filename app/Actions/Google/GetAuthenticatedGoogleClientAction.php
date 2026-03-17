<?php

namespace App\Actions\Google;

use Exception;
use Google_Client;
use Illuminate\Support\Facades\Cache;

class GetAuthenticatedGoogleClientAction
{
    public function execute(): Google_Client
    {
        $client = GoogleClientFactory::create();

        $accessToken = Cache::get('google:drive:access-token');

        if ($accessToken) {
            $client->setAccessToken($accessToken);
        }

        if ($client->isAccessTokenExpired()) {
            $refreshToken = Cache::get('google:drive:refresh-token');

            if (! $refreshToken) {
                throw new Exception('Missing Google Drive refresh token.');
            }

            $newAccessToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);

            if (isset($newAccessToken['error'])) {
                throw new Exception('Failed to refresh Google Drive access token: '.$newAccessToken['error_description']);
            }

            Cache::put('google:drive:access-token', $newAccessToken['access_token'], $newAccessToken['expires_in']);
        }

        return $client;
    }
}

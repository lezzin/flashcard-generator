<?php

namespace App\Actions\Google;

use Illuminate\Support\Facades\Cache;

class CallbackAction
{
    public function execute(string $code): array
    {
        $client = GoogleClientFactory::create();

        $result = $client->fetchAccessTokenWithAuthCode($code);

        if (isset($result['refresh_token'])) {
            Cache::put('google:drive:refresh-token', $result['refresh_token']);
        }

        if (isset($result['access_token'])) {
            Cache::put('google:drive:access-token', $result['access_token'], $result['expires_in']);
        }

        return $result;
    }
}

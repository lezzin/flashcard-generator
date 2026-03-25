<?php

namespace App\Services\Google;

use Google_Client;

class GoogleClientFactory
{
    public function create(): Google_Client
    {
        $client = new Google_Client();

        $client->setClientId(config('filesystems.disks.google.clientId'));
        $client->setClientSecret(config('filesystems.disks.google.clientSecret'));
        $client->setRedirectUri(url('/google/callback'));

        $client->setAccessType('offline');
        $client->setPrompt('consent');

        return $client;
    }
}

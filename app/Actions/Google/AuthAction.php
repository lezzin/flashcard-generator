<?php

namespace App\Actions\Google;

class AuthAction
{
    public function execute(): string
    {
        $client = GoogleClientFactory::create();

        $client->addScope('https://www.googleapis.com/auth/drive');

        return $client->createAuthUrl();
    }
}

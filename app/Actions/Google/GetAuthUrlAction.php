<?php

namespace App\Actions\Google;

use App\Services\Google\GoogleClientFactory;

class GetAuthUrlAction
{
    public function __construct(
        private readonly GoogleClientFactory $factory
    ) {
    }

    public function execute(): string
    {
        $client = $this->factory->create();
        $client->addScope('https://www.googleapis.com/auth/drive');

        return $client->createAuthUrl();
    }
}

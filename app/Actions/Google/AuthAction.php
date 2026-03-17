<?php

namespace App\Actions\Google;

use App\Services\Google\GoogleClientFactory;

class AuthAction
{
    public function __construct(
        private readonly GoogleClientFactory $factory
    ) {}

    public function execute(): string
    {
        $client = $this->factory->create();

        return $client->createAuthUrl();
    }
}

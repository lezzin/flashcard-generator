<?php

namespace App\Actions\Google;

use App\Services\Google\GoogleAuthService;
use App\Services\Google\GoogleClientFactory;

class HandleCallbackAction
{
    public function __construct(
        private readonly GoogleClientFactory $factory,
        private readonly GoogleAuthService $authService
    ) {
    }

    public function execute(string $code): array
    {
        $client = $this->factory->create();

        $token = $client->fetchAccessTokenWithAuthCode($code);

        $this->authService->saveToken($token);

        return [
            'status' => 'success',
            'message' => 'Connected successfully!',
        ];
    }
}

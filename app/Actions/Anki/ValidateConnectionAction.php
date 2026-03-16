<?php

namespace App\Actions\Anki;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ValidateConnectionAction
{
    public function execute(): void
    {
        try {
            app(InvokeAction::class)->execute('version');
        } catch (Throwable $e) {
            throw new HttpException(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                'Erro ao validar conexão com o Anki.',
                $e
            );
        }
    }
}

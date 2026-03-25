<?php

namespace App\Exceptions\Parser;

class ParserInvalidResponseException extends ParserException
{
    public function __construct()
    {
        parent::__construct(
            'O serviço de parsing retornou uma resposta inválida.'
        );
    }
}

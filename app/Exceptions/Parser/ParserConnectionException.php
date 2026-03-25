<?php

namespace App\Exceptions\Parser;

class ParserConnectionException extends ParserException
{
    public function __construct()
    {
        parent::__construct(
            'Não foi possível conectar ao serviço de parsing de PDF.'
        );
    }
}

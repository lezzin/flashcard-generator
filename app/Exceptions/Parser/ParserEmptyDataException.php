<?php

namespace App\Exceptions\Parser;

class ParserEmptyDataException extends ParserException
{
    public function __construct()
    {
        parent::__construct(
            'Não foi possível obter conteúdo do PDF processado.'
        );
    }
}

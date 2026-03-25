<?php

namespace App\Exceptions\Anki;

class AnkiConnectionException extends AnkiException
{
    public function __construct()
    {
        parent::__construct(
            'Não foi possível conectar ao Anki. Verifique se está aberto e com AnkiConnect ativo.'
        );
    }
}

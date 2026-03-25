<?php

namespace App\Exceptions\Anki;

class AnkiResponseException extends AnkiException
{
    public function __construct(string $error)
    {
        parent::__construct("Erro retornado pelo Anki: {$error}");
    }
}

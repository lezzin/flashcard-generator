<?php

namespace App\Prompts;

class FlashcardHighlightPrompt
{
  public static function handle(array $texts): string
  {
    $json = json_encode($texts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    return <<<PROMPT
Extraia até 3 palavras-chave (1-3 palavras) que aparecem em cada texto.

Retorne apenas JSON no formato:

{
 "results":[
   {"keywords":[]}
 ]
}

Textos:
{$json}
PROMPT;
  }
}

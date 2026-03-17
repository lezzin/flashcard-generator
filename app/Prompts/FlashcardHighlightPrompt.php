<?php

namespace App\Prompts;

class FlashcardHighlightPrompt
{
    public static function handle(array $texts): string
    {
        $json = json_encode($texts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        return <<<PROMPT
Você é um especialista em memorização e técnica de flashcards.
Sua tarefa é identificar os termos mais importantes de cada texto para destaque (highlight).

Instruções:
- Selecione de 1 a 3 termos ou expressões curtas (máximo 3 palavras) por texto.
- Priorize substantivos, verbos de ação ou conceitos centrais que ajudem a gatilhar a memória sobre o conteúdo total.
- Os termos DEVEM existir exatamente como estão escritos no texto original.
- Retorne apenas o JSON solicitado.

Formato de Saída:
{
 "results":[
   {"keywords": ["termo1", "termo2"]}
 ]
}

Textos para analisar:
{$json}
PROMPT;
    }
}

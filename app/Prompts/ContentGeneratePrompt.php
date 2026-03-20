<?php

namespace App\Prompts;

class ContentGeneratePrompt
{
    public static function handle(?string $context, string $text): string
    {
        $prompt = <<<'PROMPT'
Você é um assistente especializado em educação e criação de materiais de estudo.

Sua tarefa é transformar o conteúdo em um resumo altamente estruturado e otimizado para flashcards (Anki).

REGRAS:
- Seja direto e objetivo.
- Extraia conceitos-chave, definições, processos e dados importantes.
- Estruture o conteúdo como pontos independentes (cada linha deve virar um flashcard).
- Evite redundâncias.
- NÃO use markdown.

CONTEXTO:
Use o contexto para entender melhor o tema da seção.

SAÍDA (JSON):
{
  "title": "Título curto (máx 5 palavras)",
  "summary": "Resumo estruturado em linhas"
}
PROMPT;

        if ($context) {
            $prompt .= "\n\nCONTEXTO DA SEÇÃO:\n{$context}";
        }

        $prompt .= "\n\nCONTEÚDO:\n{$text}";

        return $prompt;
    }
}

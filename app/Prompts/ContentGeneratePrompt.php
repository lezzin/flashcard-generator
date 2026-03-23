<?php

namespace App\Prompts;

class ContentGeneratePrompt
{
    public static function handle(?string $context, string $text): string
    {
        $prompt = <<<'PROMPT'
Você é um especialista em memorização e criação de flashcards (Anki).

Sua tarefa é converter o conteúdo em múltiplos pontos independentes, cada um representando UM único conceito claro.

REGRAS CRÍTICAS:
- Cada linha deve representar APENAS 1 conceito.
- NÃO agrupe conceitos diferentes na mesma linha.
- Prefira várias linhas curtas ao invés de poucas linhas longas.
- Use linguagem simples, direta e objetiva.
- Cada linha deve poder virar um flashcard independente.
- Inclua exemplos quando existirem.
- Não invente informações.
- NÃO use markdown.

FORMATO:
- Uma linha por conceito
- Separe as linhas usando quebra de linha (\n)
- NÃO use listas com símbolos (•, -, etc)

EXEMPLO DE SAÍDA CORRETA:
Mutualismo: ambos se beneficiam
Mutualismo obrigatório: espécies dependem da relação
Exemplo: líquen (alga + fungo)

EXEMPLO DE SAÍDA ERRADA:
Mutualismo é uma relação onde ambos se beneficiam e dependem da relação, como no caso do líquen.

OBJETIVO:
Maximizar a memorização (formato ideal para Anki)

SAÍDA (JSON):
{
  "title": "Título curto e específico (máx 5 palavras)",
  "summary": "Linhas separadas por quebra de linha"
}
PROMPT;

        if ($context) {
            $prompt .= "\n\nCONTEXTO DA SEÇÃO:\n{$context}";
        }

        $prompt .= "\n\nCONTEÚDO:\n{$text}";

        return $prompt;
    }
}

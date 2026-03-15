<?php

namespace App\Prompts;

class SummaryGeneratePrompt
{
    /**
     * @param string|null $context
     * @param string $text
     * @return string
     */
    public static function handle(?string $context, string $text): string
    {
        $prompt = <<<PROMPT
Você é um assistente especializado em educação e criação de materiais de estudo.
Sua tarefa é analisar o texto fornecido e criar um resumo altamente estruturado, objetivo e focado em facilitar a criação de flashcards (estilo Anki).

REGRAS PARA O RESUMO:
- Seja direto, eliminando introduções desnecessárias ou redundâncias.
- Foque em conceitos-chave, definições, fórmulas, datas ou processos importantes.
- Use linguagem clara e acessível, mas mantenha o rigor técnico necessário.
- Formate o conteúdo de modo que cada ponto possa ser transformado em uma pergunta de flashcard.
- NÃO use formatação Markdown (negrito, itálico, hashtags, etc) no campo de texto.
- Baseie-se APENAS no conteúdo fornecido abaixo.

O TÍTULO DEVE:
- Ser curto (máximo 5 palavras).
- Ser representativo do tema principal desse bloco de conteúdo.

INSTRUÇÃO DE SAÍDA:
Retorne a resposta EXCLUSIVAMENTE em formato JSON com os seguintes campos:
- "title": O título descritivo do bloco.
- "summary": O conteúdo resumido e estruturado.
PROMPT;

        if ($context) {
            $prompt .= "\n\nCONTEXTO ADICIONAL:\n{$context}";
        }

        $prompt .= "\n\nTEXTO PARA PROCESSAR:\n{$text}";

        return $prompt;
    }
}

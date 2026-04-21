<?php

namespace App\Prompts;

use Gemini\Data\Schema;
use Gemini\Enums\DataType;

class ContentGeneratePrompt
{
    public static function handle(string $content): string
    {
        $prompt = <<<'PROMPT'
Você é um especialista em estruturação de conteúdo para sistemas de flashcards.

Sua função é REORGANIZAR o conteúdo em unidades conceituais claras, mantendo TODA a informação original.

OBJETIVO:
Transformar o texto em blocos de conhecimento bem definidos, sem perda de informação.

REGRAS CRÍTICAS:
- NÃO resuma o conteúdo.
- NÃO remova informações.
- NÃO adicione novos fatos.
- NÃO simplifique conceitos.
- Apenas reorganize o texto para facilitar a criação de flashcards.
- Separe ideias quando forem conceitos diferentes.
- Agrupe apenas quando forem parte da mesma definição lógica.

FORMATO:
- Um conceito por linha ou bloco curto
- Sem markdown
- Sem listas com símbolos
- Use linguagem neutra e direta

EXEMPLO:
Conceito A: definição completa
Conceito B: definição completa
Conceito C: definição completa

OBJETIVO FINAL:
Gerar base completa e fiel ao conteúdo original para criação de flashcards.
PROMPT;

        $prompt .= "\n\nCONTEÚDO:\n{$content}";

        return $prompt;
    }

    public static function schema(): Schema
    {
        return new Schema(
            type: DataType::OBJECT,
            properties: [
                'title' => new Schema(type: DataType::STRING),
                'content' => new Schema(type: DataType::STRING),
            ],
            required: ['title', 'content'],
        );
    }
}

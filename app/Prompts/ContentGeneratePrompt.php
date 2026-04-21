<?php

namespace App\Prompts;

use Gemini\Data\Schema;
use Gemini\Enums\DataType;

class ContentGeneratePrompt
{
    public static function handle(string $content): string
    {
        $prompt = <<<'PROMPT'
    Você é um especialista em memorização e criação de flashcards (Anki).

    Sua tarefa é transformar o conteúdo em unidades de estudo independentes e bem estruturadas.

    REGRAS CRÍTICAS:
    - Cada linha deve conter UM conceito completo e memorizável.
    - NÃO fragmente excessivamente conceitos que fazem sentido juntos.
    - Agrupe características relacionadas no mesmo conceito quando forem parte da mesma definição.
    - Prefira clareza e compreensão, não atomização extrema.
    - Use linguagem simples e direta.
    - Inclua exemplos quando ajudarem na memorização.
    - Não invente informações.
    - NÃO use markdown.

    FORMATO:
    - Uma linha por flashcard
    - Separe com quebra de linha (\n)
    - Sem bullets ou símbolos

    BOA PRÁTICA:
    - Definições completas devem permanecer juntas
    - Listas conceituais podem ser agrupadas se forem parte do mesmo tema
    - Só separar quando realmente forem conceitos independentes

    EXEMPLO CORRETO:
    Mutualismo: relação ecológica em que ambas as espécies se beneficiam
    Exemplo: líquen (alga + fungo)

    CF/88: Constituição promulgada, rígida, analítica, formal, dogmática e eclética

    EXEMPLO ERRADO:
    CF/88: promulgada
    CF/88: rígida
    CF/88: analítica

    OBJETIVO:
    Criar flashcards ideais para memorização profunda e compreensão conceitual.
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

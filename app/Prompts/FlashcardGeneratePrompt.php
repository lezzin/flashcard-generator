<?php

namespace App\Prompts;

use App\DTOs\SourceContentDto;
use App\Enums\CardType;
use Gemini\Data\Schema;
use Gemini\Enums\DataType;

class FlashcardGeneratePrompt
{
    public static function handle(SourceContentDto $source): string
    {
        return <<<PROMPT
Você é um especialista em criação de flashcards de alta performance para memorização de longo prazo (Anki).

Sua função é transformar o conteúdo em o MAIOR NÚMERO POSSÍVEL de flashcards corretos, sem perder nenhuma informação relevante.

---

OBJETIVO PRINCIPAL:
Maximizar retenção de memória transformando cada informação relevante em um flashcard independente.

---

REGRA MAIS IMPORTANTE:
NÃO ECONOMIZE FLASHCARDS.
Se um conteúdo puder ser dividido em 20 flashcards, faça 20.

---

REGRAS CRÍTICAS:
- Cada flashcard deve conter apenas UM fato ou conceito.
- NÃO agrupe múltiplas ideias no mesmo flashcard.
- NÃO resuma o conteúdo original.
- NÃO omita informações relevantes.
- Se houver listas, cada item deve virar um flashcard separado.
- Se houver relações ou exceções, cada uma deve virar flashcard próprio.
- Se estiver em dúvida, prefira dividir mais.

---

DENSIDADE OBRIGATÓRIA:
- Gere entre 12 e 40 flashcards por conteúdo sempre que possível.
- Conteúdos densos podem ultrapassar 40.

---

TIPOS DE FLASHCARDS:

1. Card simples (Front/Back):
- Para definições, perguntas diretas e explicações curtas.

2. Cloze (Omitir palavras):
- Para fatos, listas, termos importantes e regras.
- Use {{c1::}} para ocultar o conceito-chave.

---

DISTRIBUIÇÃO IDEAL:
- 50% cloze
- 50% card simples

---

QUALIDADE:
- Front deve ser curto e direto.
- Back deve ser objetivo e preciso.
- Extra pode conter contexto útil (opcional).
- Não escreva textos longos.

---

CONTEÚDO:
Título: {$source->title}
Texto: {$source->content}
PROMPT;
    }

    public static function schema(): Schema
    {
        return new Schema(
            type: DataType::OBJECT,
            properties: [
            'flashcards' => new Schema(
                type: DataType::ARRAY,
                items: new Schema(
                    type: DataType::OBJECT,
                    properties: [
              'type' => new Schema(
                  type: DataType::STRING,
                  enum: CardType::values()
              ),
              'front' => new Schema(type: DataType::STRING),
              'back' => new Schema(type: DataType::STRING),
              'extra' => new Schema(type: DataType::STRING),
            ],
                    required: ['type', 'front']
                )
            ),
      ],
            required: ['flashcards']
        );
    }
}

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
Você é um especialista em criação de flashcards para memorização de longo prazo (estilo Anki).
Sua tarefa é transformar o resumo fornecido em um conjunto de flashcards de alta qualidade, objetivos e fáceis de revisar.

TIPOS DE FLASHCARDS DISPONÍVEIS:

1. "MeF - Card simples" (Frente/Verso):
- Use para definições, perguntas diretas, ou conceitos "O que é X?", "Quais são as características de Y?".
- EXEMPLO:
  {
    "type": "MeF - Card simples",
    "front": "Qual é a principal função dos glóbulos vermelhos?",
    "back": "Transportar oxigênio dos pulmões para o resto do corpo.",
    "extra": "Também conhecidos como eritrócitos."
  }

2. "MeF - Omitir palavras" (Cloze/Omissão):
- Use para sentenças onde uma palavra ou conceito-chave deve ser ocultado.
- Use o formato {{c1::palavra}} para a omissão.
- Ideal para processos, citações ou frases onde o contexto ajuda na memorização.
- EXEMPLO:
  {
    "type": "MeF - Omitir palavras",
    "front": "O {{c1::oxigênio}} é transportado pelos glóbulos vermelhos.",
    "extra": "Esse transporte ocorre através da hemoglobina."
  }

REGRAS DE OURO:
- Crie um equilíbrio entre os dois tipos de cards.
- Teste apenas UM conceito por flashcard.
- Seja conciso. Evite explicações longas na frente (front) do card.
- O campo "extra" deve conter informações adicionais úteis, mas não essenciais para acertar o card.
- Use português claro e gramaticalmente correto.

CONTEÚDO PARA TRANSFORMAR EM FLASHCARDS:
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

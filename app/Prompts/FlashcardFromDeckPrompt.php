<?php

namespace App\Prompts;

use App\DTOs\SourceContentDto;

class FlashcardFromDeckPrompt
{
    public static function handle(SourceContentDto $source): string
    {
        return <<<PROMPT
Você é um especialista em criação de flashcards para memorização de longo prazo (estilo Anki).
Sua tarefa é analisar os flashcards existentes e gerar NOVOS flashcards complementares que aprofundem o conhecimento ou cubram lacunas, SEM repetir o que já existe.

ESTES SÃO FLASHCARDS QUE JÁ EXISTEM NO MEU DECK (Título: {$source->title}):
---
{$source->content}
---

TIPOS DE FLASHCARDS QUE VOCÊ DEVE GERAR:

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
    "extra": "Esse transporte ocorre através da hemoglobina.",
    "deck": "Biologia::Sistema Circulatório"
    }

    REGRAS DE OURO:
    - O campo "deck" é opcional. Use-o se quiser organizar o card em um sub-tópico específico (ex: "{$source->title}::Subtópico"). Se omitido, o card será adicionado ao deck "{$source->title}".
    - NÃO REPITAS INFORMAÇÕES QUE JÁ ESTÃO NOS FLASHCARDS EXISTENTES.

- Gere flashcards que tragam novos ângulos, detalhes técnicos ou aplicações práticas do assunto.
- Teste apenas UM conceito por flashcard.
- Seja conciso. Evite explicações longas na frente (front) do card.
- O campo "extra" deve conter informações adicionais úteis, mas não essenciais para acertar o card.
- Use português claro e gramaticalmente correto.

INSTRUÇÃO DE SAÍDA:
Retorne EXCLUSIVAMENTE um objeto JSON contendo uma lista de "flashcards".
PROMPT;
    }
}

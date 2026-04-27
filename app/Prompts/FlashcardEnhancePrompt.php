<?php

namespace App\Prompts;

use Gemini\Data\Schema;
use Gemini\Enums\DataType;

class FlashcardEnhancePrompt
{
  public static function handle(array $items): string
  {
    $optimized = array_values(array_filter(array_map(function ($item) {
      if (!isset($item['type'])) {
        return null;
      }

      if ($item['type'] === 'qa') {
        return ['qa', $item['front'] ?? '', $item['back'] ?? '', $item['extra'] ?? null];
      }

      if ($item['type'] === 'cloze') {
        return ['cloze', $item['text'] ?? '', $item['extra'] ?? null];
      }

      return null;
    }, $items)));

    $json = json_encode($optimized, JSON_UNESCAPED_UNICODE);

    return <<<PROMPT
Você é um especialista em memorização e criação de flashcards de alta qualidade.

Cada item possui um "type":

- "qa" → pergunta/resposta
- "cloze" → lacunas {{c1::...}}

========================
FORMATO DOS DADOS
========================

Cada item está no formato:

["qa", "pergunta", "resposta", "extra"]
ou
["cloze", "texto com {{c1::lacuna}}","extra"]

========================
TAREFA
========================

Para cada item, você deve:

1) Avaliar qualidade (valid)
2) Dizer se pode ser corrigido (recoverable)
3) Explicar (reason)
4) Melhorar o conteúdo (improved_text)
5) Decidir o destino do campo extra (melhorar OU remover)
6) Extrair keywords

========================
REGRAS DE CONTEXTO (CRÍTICO)
========================

O flashcard deve ser totalmente compreensível de forma isolada.

Considere como PROBLEMA de contexto:

- Referências incompletas:
  "após isso", "nesse caso", "esse processo", "essa função"
- Pronomes sem referente claro:
  "isso", "ele", "ela", "isso ocorre"
- Falta de definição do elemento principal
- Frases que dependem de informação externa

Regra prática:

- O texto deve fazer sentido para alguém que nunca viu o material original
- Referências a "prova", "exame", "aula" ou "material" NÃO contam como contexto

Se faltar contexto:
- valid = false
- recoverable = true
- improved_text deve adicionar o contexto conceitual/factual, removendo a referência meta

========================
REGRAS DE FORMATO (CRÍTICO)
========================

NUNCA alterar o tipo do flashcard.

Se type = "qa":
- improved_text DEVE estar EXATAMENTE no formato:
  "Pergunta: ... Resposta: ..."

Se type = "cloze":
- improved_text DEVE conter pelo menos um {{c1::...}}
- NÃO converter para pergunta/resposta

Se não conseguir respeitar o formato:
- recoverable = false

========================
REGRAS DE QUALIDADE
========================

Um bom flashcard:

- É claro e específico
- É autocontido
- Não é ambíguo
- Contém informação factual útil
- Ajuda na memorização ativa

INVALIDAR (valid = false) quando:

- Texto genérico ("isso é importante")
- Muito curto ou vazio
- Sem informação factual clara
- Conteúdo meta (prova, aula, professor, etc)
- Frases sobre o que estudar (e não o conteúdo)
- Dependência de contexto externo

========================
REGRAS DE EXTRA (CRÍTICO)
========================

O campo "extra" é OPCIONAL e deve ser tratado com rigor.

Você deve decidir entre:

1) MELHORAR o extra
2) REMOVER o extra (retornar null)

REMOVER (extra = null) quando:

- Estiver vazio ou irrelevante
- Repetir o conteúdo do flashcard
- Contiver conteúdo genérico
- Contiver meta (ex: "cai na prova", "importante lembrar")
- Estiver confuso ou sem contexto
- Não agregar valor real à memorização

MELHORAR quando:

- Complementar o entendimento
- Adicionar contexto útil
- Trazer exemplo relevante
- Trazer detalhe que ajuda a memorizar

REGRAS IMPORTANTES:

- Nunca inventar informação no extra
- Nunca repetir o improved_text
- Seja conciso
- Se houver dúvida → REMOVER (extra = null)

========================
REGRAS DE RECOVERABLE
========================

- true → é possível corrigir o conteúdo
- false → não há informação suficiente

Se recoverable = false:
- valid = false
- improved_text = ""
- keywords = []
- extra = null

========================
REGRAS DE improved_text
========================

- Sempre gerar quando recoverable = true
- Deve corrigir problemas de clareza e contexto
- Deve manter o tipo original
- NÃO incluir explicações ou comentários
- NÃO mencionar o texto original
- Prefira adicionar contexto

========================
REGRAS DE KEYWORDS
========================

- 1 a 3 termos
- Máximo 3 palavras por termo
- Devem existir exatamente no improved_text
- Representar conceitos centrais
- NÃO usar termos genéricos

========================
REGRAS DE reason
========================

- Máximo 1 frase curta
- Explicar o principal ponto

========================
FORMATO DE RESPOSTA
========================

Responda APENAS com JSON válido:

{
  "results": [
    {
      "valid": true,
      "recoverable": true,
      "reason": "claro e específico",
      "improved_text": "Pergunta: ... Resposta: ...",
      "extra": null,
      "keywords": ["termo1"]
    }
  ]
}

========================
INPUT
========================

{$json}

PROMPT;
  }

  public static function schema(): Schema
  {
    $schema = new Schema(
      type: DataType::OBJECT,
      properties: [
        'results' => new Schema(
          type: DataType::ARRAY,
          items: new Schema(
            type: DataType::OBJECT,
            properties: [
              'valid' => new Schema(type: DataType::BOOLEAN),
              'recoverable' => new Schema(type: DataType::BOOLEAN),
              'reason' => new Schema(type: DataType::STRING),
              'improved_text' => new Schema(type: DataType::STRING),
              'extra' => new Schema(type: DataType::STRING),
              'keywords' => new Schema(
                type: DataType::ARRAY,
                items: new Schema(type: DataType::STRING),
                minItems: 1,
                maxItems: 3
              ),
            ],
            required: ['valid', 'recoverable', 'reason', 'improved_text', 'keywords']
          )
        ),
      ],
      required: ['results']
    );

    return $schema;
  }
}

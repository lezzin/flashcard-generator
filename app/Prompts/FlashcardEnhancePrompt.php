<?php

namespace App\Prompts;

class FlashcardEnhancePrompt
{
  public static function handle(array $items): string
  {
    $optimized = array_values(array_filter(array_map(function ($item) {
      if (!isset($item['type'])) {
        return null;
      }

      if ($item['type'] === 'qa') {
        return ['qa', $item['front'] ?? '', $item['back'] ?? '', $item['extra'] ?? ''];
      }

      if ($item['type'] === 'cloze') {
        return ['cloze', $item['text'] ?? '', $item['extra'] ?? ''];
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
5) Melhorar o conteúdo extra ou remover o existente
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
REGRAS DE RECOVERABLE
========================

- true → é possível corrigir o conteúdo
- false → não há informação suficiente

Se recoverable = false:
- valid = false
- improved_text = ""
- keywords = []

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
      "extra": "Conteúdo extra",
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
}

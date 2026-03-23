<?php

namespace App\Actions\Anki\Highlighting;

use App\Actions\Gemini\GenerateJsonAction;
use App\Enums\CardType;
use App\Prompts\FlashcardEnhancePrompt;
use Gemini\Data\Schema;
use Gemini\Enums\DataType;
use Illuminate\Support\Collection;

class HighlightNoteAction
{
    protected const COLORS = [
        'background-color: #FFE0B2; color: #D84315; font-weight: bold;',
        'background-color: #E1F5FE; color: #0277BD; font-weight: bold;',
        'background-color: #F1F8E9; color: #33691E; font-weight: bold;',
    ];

    public function __construct(
        private readonly GenerateJsonAction $generateJsonAction
    ) {}

    public function execute(array|Collection $notes): array|Collection
    {
        $isCollection = $notes instanceof Collection;
        $notesCollection = $isCollection ? $notes : collect([$notes]);

        $payloads = $this->buildPayloads($notesCollection);
        $resultsFromAI = $this->enhance($payloads);

        $results = $notesCollection->values()->map(function ($note, $index) use ($resultsFromAI) {
            $ai = $resultsFromAI[$index] ?? null;

            if (!$ai) {
                return $note;
            }

            $improvedText = trim($ai->improved_text ?? '');
            $keywords = $ai->keywords ?? [];

            $note['ai'] = [
                'valid' => $ai->valid ?? null,
                'recoverable' => $ai->recoverable ?? null,
                'reason' => $ai->reason ?? null,
            ];

            if (!($ai->recoverable ?? true)) {
                $note['invalid'] = true;
                return $note;
            }

            if (!empty($improvedText)) {
                $note = $this->applyImprovedText($note, $improvedText);
            }

            return $this->applyStylingToFields($note, $keywords);
        });

        return $isCollection ? $results : $results->first();
    }

    protected function buildPayloads(Collection $notes): array
    {
        return $notes->map(function ($note) {
            $type = CardType::tryFrom($note['modelName']);

            if ($type === CardType::SIMPLE) {
                return [
                    'type' => 'qa',
                    'front' => $note['fields']['Frente'] ?? '',
                    'back' => $note['fields']['Verso'] ?? '',
                ];
            }

            if ($type === CardType::CLOZE) {
                return [
                    'type' => 'cloze',
                    'text' => $note['fields']['Texto'] ?? '',
                ];
            }

            return null;
        })->filter()->values()->toArray();
    }

    protected function applyImprovedText(array $note, string $improvedText): array
    {
        $type = CardType::tryFrom($note['modelName']);

        if ($type === CardType::SIMPLE) {
            if (preg_match('/Pergunta:\s*(.*?)\s*Resposta:\s*(.*)/is', $improvedText, $matches)) {
                $note['fields']['Frente'] = trim($matches[1]);
                $note['fields']['Verso'] = trim($matches[2]);
            }
        }

        if ($type === CardType::CLOZE) {
            if ($this->isValidCloze($improvedText)) {
                $note['fields']['Texto'] = $improvedText;
            }
        }

        return $note;
    }

    protected function isValidCloze(string $text): bool
    {
        return preg_match('/\{\{c\d+::.+?\}\}/', $text);
    }

    protected function enhance(array $payloads): array
    {
        if (empty($payloads)) {
            return [];
        }

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
                            'keywords' => new Schema(
                                type: DataType::ARRAY,
                                items: new Schema(type: DataType::STRING),
                                minItems: 0,
                                maxItems: 3
                            ),
                        ],
                        required: ['valid', 'recoverable', 'reason', 'improved_text', 'keywords']
                    )
                ),
            ],
            required: ['results']
        );

        $data = $this->generateJsonAction->execute(
            FlashcardEnhancePrompt::handle($payloads),
            $schema
        );

        return $data->results ?? [];
    }

    protected function applyStyling(string $text, array $keywords): string
    {
        $colorIndex = 0;

        foreach ($keywords as $keyword) {
            if (empty($keyword)) continue;

            $style = self::COLORS[$colorIndex % count(self::COLORS)];

            $pattern = '/(<[^>]+>)|(\b' . preg_quote($keyword, '/') . '\b)/iu';

            $text = preg_replace_callback($pattern, function ($matches) use ($style) {
                if (!empty($matches[1])) {
                    return $matches[1];
                }

                return "<span style=\"$style\">{$matches[2]}</span>";
            }, $text);

            $colorIndex++;
        }

        return $text;
    }

    protected function applyStylingToFields(array $note, array $keywords): array
    {
        $type = CardType::tryFrom($note['modelName']);

        if ($type === CardType::SIMPLE) {
            $note['fields']['Frente'] = $this->applyStyling($note['fields']['Frente'], $keywords);
            $note['fields']['Verso'] = strip_tags($note['fields']['Verso']);
            $note['fields']['Extra'] = strip_tags($note['fields']['Extra']);
        }

        if ($type === CardType::CLOZE) {
            $note['fields']['Texto'] = $this->applyStyling($note['fields']['Texto'], $keywords);
            $note['fields']['Extra'] = strip_tags($note['fields']['Extra']);
        }

        return $note;
    }
}

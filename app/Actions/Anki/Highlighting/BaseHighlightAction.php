<?php

namespace App\Actions\Anki\Highlighting;

use App\Enums\CardType;
use Illuminate\Support\Collection;

abstract class BaseHighlightAction
{
    protected const COLORS = [
        'background-color: #FFE0B2; color: #D84315; font-weight: bold;',
        'background-color: #E1F5FE; color: #0277BD; font-weight: bold;',
        'background-color: #F1F8E9; color: #33691E; font-weight: bold;',
    ];

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
            $note['fields']['Frente'] = $this->applyStyling($note['fields']['Frente'] ?? '', $keywords);
            $note['fields']['Verso'] = strip_tags($note['fields']['Verso'] ?? '');
            $note['fields']['Extra'] = strip_tags($note['fields']['Extra'] ?? '');
        }

        if ($type === CardType::CLOZE) {
            $note['fields']['Texto'] = $this->applyStyling($note['fields']['Texto'] ?? '', $keywords);
            $note['fields']['Extra'] = strip_tags($note['fields']['Extra'] ?? '');
        }

        return $note;
    }

    protected function getNoteHash(array $note): string
    {
        return md5($note['modelName'] . json_encode($note['fields']));
    }
}

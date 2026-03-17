<?php

namespace App\Actions\Flashcard;

use App\Actions\Gemini\GenerateJsonAction;
use App\Enums\CardType;
use App\Prompts\FlashcardHighlightPrompt;
use Gemini\Data\Schema;
use Gemini\Enums\DataType;

abstract class BaseHighlightAction
{
    protected const COLORS = [
        'background-color: #FFE0B2; color: #D84315; font-weight: bold;',
        'background-color: #E1F5FE; color: #0277BD; font-weight: bold;',
        'background-color: #F1F8E9; color: #33691E; font-weight: bold;',
    ];

    public function __construct(
        private readonly GenerateJsonAction $generateJsonAction
    ) {}

    protected function applyStyling(string $text, array $keywords): string
    {
        $colorIndex = 0;

        foreach ($keywords as $keyword) {
            if (empty($keyword)) {
                continue;
            }

            $style = self::COLORS[$colorIndex % count(self::COLORS)];

            // Match tags OR the keyword
            $pattern = '/(<[^>]+>)|(\b'.preg_quote($keyword, '/').'\b)/i';

            $replaced = false;
            $text = preg_replace_callback($pattern, function ($matches) use ($style, &$replaced) {
                // If it's a tag (Group 1 matched), return it as is
                if (! empty($matches[1])) {
                    return $matches[1];
                }

                // If it's the keyword (Group 2 matched) and not yet replaced
                if (! $replaced) {
                    $replaced = true;

                    return "<span style=\"$style\">".$matches[2].'</span>';
                }

                return $matches[2];
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
        }

        if ($type === CardType::CLOZE) {
            $note['fields']['Texto'] = $this->applyStyling($note['fields']['Texto'], $keywords);
        }

        return $note;
    }

    public function extractFieldsToUpdate(array $note): array
    {
        $type = CardType::tryFrom($note['modelName']);

        $fields = match ($type) {
            CardType::CLOZE => ['Texto' => $note['fields']['Texto'] ?? null],
            CardType::SIMPLE => ['Frente' => $note['fields']['Frente'] ?? null],
            default => [],
        };

        return array_filter($fields);
    }

    protected function extractText(array $note): ?string
    {
        $type = CardType::tryFrom($note['modelName']);

        if ($type === CardType::SIMPLE) {
            return $note['fields']['Frente'] ?? null;
        }

        if ($type === CardType::CLOZE) {
            return $note['fields']['Texto'] ?? null;
        }

        return null;
    }

    protected function getKeywords(array $texts): array
    {
        $schema = new Schema(
            type: DataType::OBJECT,
            properties: [
                'results' => new Schema(
                    type: DataType::ARRAY,
                    items: new Schema(
                        type: DataType::OBJECT,
                        properties: [
                            'keywords' => new Schema(
                                type: DataType::ARRAY,
                                items: new Schema(type: DataType::STRING),
                                minItems: 1,
                                maxItems: 3
                            ),
                        ],
                        required: ['keywords']
                    )
                ),
            ],
            required: ['results']
        );

        $data = $this->generateJsonAction->execute(
            FlashcardHighlightPrompt::handle($texts),
            $schema
        );

        return $data->results ?? [];
    }
}

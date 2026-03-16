<?php

namespace App\Actions\Flashcard;

use App\Actions\Gemini\GenerateJsonAction;
use App\Enums\CardTypes;
use App\Prompts\FlashcardHighlightPrompt;
use Gemini\Data\Schema;
use Gemini\Enums\DataType;
use Illuminate\Support\Collection;

class HighlightKeywordsAction
{
    private const COLORS = [
        'background-color: #FFE0B2; color: #D84315;',
        'background-color: #E1F5FE; color: #0277BD;',
        'background-color: #F1F8E9; color: #33691E;',
    ];

    public function __construct(
        private readonly GenerateJsonAction $generateJsonAction
    ) {}

    public function execute(Collection $notes): Collection
    {
        $texts = $this->extractTexts($notes);
        $keywordsList = $this->getKeywords($texts);

        return $notes->values()->map(function ($note, $index) use ($keywordsList) {
            $keywords = $keywordsList[$index]->keywords ?? [];

            if ($note['modelName'] === CardTypes::CARD_SIMPLE->value) {
                $note['fields']['Frente'] = $this->applyStyling($note['fields']['Frente'], $keywords);
            }

            if ($note['modelName'] === CardTypes::CARD_OMIT->value) {
                $note['fields']['Texto'] = $this->applyStyling($note['fields']['Texto'], $keywords);
            }

            return $note;
        });
    }

    private function extractTexts(Collection $notes): array
    {
        return $notes->map(function ($note) {
            if ($note['modelName'] === CardTypes::CARD_SIMPLE->value) {
                return $note['fields']['Frente'];
            }

            if ($note['modelName'] === CardTypes::CARD_OMIT->value) {
                return $note['fields']['Texto'];
            }

            return null;
        })->filter()->values()->toArray();
    }

    private function getKeywords(array $texts): array
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

    private function applyStyling(string $text, array $keywords): string
    {
        $colorIndex = 0;

        foreach ($keywords as $keyword) {
            if (empty($keyword)) {
                continue;
            }

            $style = self::COLORS[$colorIndex % count(self::COLORS)];

            $pattern = '/\b('.preg_quote($keyword, '/').')\b/i';
            $replacement = "<span style=\"$style\">$1</span>";

            $text = preg_replace($pattern, $replacement, $text, 1);

            $colorIndex++;
        }

        return $text;
    }
}

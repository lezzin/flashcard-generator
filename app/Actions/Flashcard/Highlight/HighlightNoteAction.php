<?php

namespace App\Actions\Flashcard\Highlight;

use App\Actions\Gemini\GenerateJsonAction;
use App\Enums\CardType;
use App\Prompts\FlashcardHighlightPrompt;
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

        $texts = $this->extractTexts($notesCollection);
        $keywordsList = $this->getKeywords($texts);

        $results = $notesCollection->values()->map(function ($note, $index) use ($keywordsList) {
            $keywords = $keywordsList[$index]->keywords ?? [];

            return $this->applyStylingToFields($note, $keywords);
        });

        return $isCollection ? $results : $results->first();
    }

    protected function applyStyling(string $text, array $keywords): string
    {
        $colorIndex = 0;

        foreach ($keywords as $keyword) {
            if (empty($keyword)) {
                continue;
            }

            $style = self::COLORS[$colorIndex % count(self::COLORS)];

            $pattern = '/(<[^>]+>)|(\b'.preg_quote($keyword, '/').'\b)/i';

            $replaced = false;
            $text = preg_replace_callback($pattern, function ($matches) use ($style, &$replaced) {
                if (! empty($matches[1])) {
                    return $matches[1];
                }

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
            $note['fields']['Verso'] = strip_tags($note['fields']['Verso']);
            $note['fields']['Extra'] = strip_tags($note['fields']['Extra']);
        }

        if ($type === CardType::CLOZE) {
            $note['fields']['Texto'] = $this->applyStyling($note['fields']['Texto'], $keywords);
            $note['fields']['Extra'] = strip_tags($note['fields']['Extra']);
        }

        return $note;
    }

    private function extractTexts(Collection $notes): array
    {
        return $notes
            ->map(fn ($note) => $this->extractTextFromNote($note))
            ->filter()
            ->values()
            ->toArray();
    }

    protected function extractTextFromNote(array $note): ?string
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
        if (empty($texts)) {
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

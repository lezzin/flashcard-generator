<?php

namespace App\Actions\Anki;

use App\Actions\Gemini\GenerateJsonAction;
use App\DTOs\GeneratedFlashcardDto;
use App\DTOs\SourceContentDto;
use App\Enums\CardType;
use App\Models\AnkiFlashcard;
use App\Prompts\FlashcardFromDeckPrompt;
use App\Prompts\FlashcardGeneratePrompt;
use Illuminate\Support\Collection;
use Gemini\Data\Schema;
use Gemini\Enums\DataType;

use function Illuminate\Support\now;

class GenerateFlashcardAction
{
    public function __construct(
        protected readonly GenerateJsonAction $generateJsonAction
    ) {}

    public function execute(
        SourceContentDto $source,
        string $baseTitle,
        string $generationType,
    ) {
        $data = $this->generateJsonAction->execute(
            $this->getPrompt($generationType, $source),
            $this->getSchema($generationType, $source),
        );

        if (!isset($data->flashcards)) {
            return;
        }

        $deckName =
            !is_null($baseTitle) ?
            "{$baseTitle}::{$source->title}" :
            $source->title;

        $flashcards = $this->toDto(
            flashcards: $data->flashcards,
            deckName: $deckName
        );

        if ($flashcards->isEmpty()) {
            return;
        }

        $toInsert = $flashcards->map(function ($flashcard) {
            $fields = $flashcard->type == CardType::CLOZE ? [
                'Texto' => $flashcard->front,
                'Extra' => $flashcard->extra,
            ] : [
                'Frente' => $flashcard->front,
                'Verso'  => $flashcard->back,
                'Extra'  => $flashcard->extra,
            ];

            return [
                'type'   => $flashcard->type,
                'fields' => json_encode($fields),
                'deck'   => $flashcard->deck,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        AnkiFlashcard::insert($toInsert);
    }

    protected function toDto(array $flashcards, string $deckName): Collection
    {
        return collect($flashcards)
            ->map(function ($card) use ($deckName) {
                $card->deck = $deckName;

                return match ($card->type) {
                    CardType::CLOZE->value => GeneratedFlashcardDto::omitFromObject($card),
                    CardType::SIMPLE->value => GeneratedFlashcardDto::simpleFromObject($card),
                    default => null,
                };
            })
            ->filter();
    }

    private function getPrompt(string $generationType, SourceContentDto $source)
    {
        return match ($generationType) {
            "deck"    => FlashcardFromDeckPrompt::handle($source),
            "content" => FlashcardGeneratePrompt::handle($source),
        };
    }

    private function getSchema(string $generationType, SourceContentDto $source)
    {
        return match ($generationType) {
            "deck"    => new Schema(
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
                                'deck' => new Schema(type: DataType::STRING),
                            ],
                            required: ['type', 'front']
                        )
                    ),
                ],
                required: ['flashcards']
            ),
            "content" => new Schema(
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
            ),
        };
    }
}

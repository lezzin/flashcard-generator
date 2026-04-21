<?php

namespace App\Actions\Anki;

use App\Actions\Gemini\GenerateJsonAction;
use App\DTOs\GeneratedFlashcardDto;
use App\DTOs\SourceContentDto;
use App\Enums\CardType;
use App\Models\AnkiFlashcard;
use App\Prompts\FlashcardGeneratePrompt;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

use function Illuminate\Support\now;

class GenerateFlashcardAction
{
    public function __construct(
        protected readonly GenerateJsonAction $generateJsonAction
    ) {
    }

    public function execute(SourceContentDto $source)
    {
        $data = $this->generateJsonAction->execute(
            FlashcardGeneratePrompt::handle($source),
            FlashcardGeneratePrompt::schema()
        );

        if (!isset($data->flashcards)) {
            Log::channel('content')->info('The generated data for flashcards is empty: ', [
                'content' => json_encode($source),
                'data'    => json_encode($data),
            ]);

            return;
        }

        Log::channel('gemini-backup')->info("[FLASHCARD GENERATED]", [
            'data' => json_encode($data),
            'timestamp' => now(),
        ]);

        $flashcards = $this->toDto(
            flashcards: $data->flashcards,
            deckName: $source->title
        );

        if ($flashcards->isEmpty()) {
            Log::channel('content')->info('Failed to transform flashcards into DTO: ', [
                'content' => json_encode($source),
                'data'    => json_encode($data),
            ]);

            return;
        }

        $toInsert = $flashcards->map(function ($flashcard) {
            $fields = $flashcard->type == CardType::CLOZE ? [
                'Texto' => $flashcard->front,
                'Extra' => $flashcard?->extra ?? null,
            ] : [
                'Frente' => $flashcard->front,
                'Verso'  => $flashcard->back,
                'Extra'  => $flashcard?->extra ?? null,
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

        app(AddFromAIToAnkiAction::class)->execute(collect($toInsert));
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
}

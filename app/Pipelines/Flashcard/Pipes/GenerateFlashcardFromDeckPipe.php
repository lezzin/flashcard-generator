<?php

namespace App\Pipelines\Flashcard\Pipes;

use App\DTOs\GeneratedFlashcardDto;
use App\Enums\CardType;
use App\Pipelines\Flashcard\FlashcardPipelineContext;
use App\Prompts\FlashcardFromDeckPrompt;
use Closure;
use Exception;
use Gemini\Data\Schema;
use Gemini\Enums\DataType;
use Illuminate\Support\Collection;

class GenerateFlashcardFromDeckPipe extends GenerateFlashcardPipe
{
    public function handle(FlashcardPipelineContext $context, Closure $next)
    {
        $context->results = $context->sources->flatMap(function ($source) use ($context) {
            try {
                $schema = new Schema(
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
                );

                $prompt = FlashcardFromDeckPrompt::handle($source);

                $data = $this->generateJsonAction->execute($prompt, $schema);

                if (isset($data->flashcards)) {
                    return $this->toDto(
                        flashcards: $data->flashcards,
                        subtitle: $source->title,
                        title: $context->title,
                    );
                }

                return collect();
            } catch (Exception $e) {
                return collect();
            }
        });

        return $next($context);
    }

    protected function toDto(array $flashcards, string $subtitle, ?string $title = null): Collection
    {
        return collect($flashcards)
            ->map(function ($card) use ($title, $subtitle) {
                $card->deck = $card->deck ?? (!is_null($title) ? "{$title}::$subtitle" : $subtitle);

                return match ($card->type) {
                    CardType::CLOZE->value => GeneratedFlashcardDto::omitFromObject($card),
                    CardType::SIMPLE->value => GeneratedFlashcardDto::simpleFromObject($card),
                    default => null,
                };
            })
            ->filter();
    }
}

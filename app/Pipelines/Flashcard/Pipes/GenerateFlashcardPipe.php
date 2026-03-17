<?php

namespace App\Pipelines\Flashcard\Pipes;

use App\Actions\Gemini\GenerateJsonAction;
use App\DTOs\GeneratedFlashcardDto;
use App\DTOs\SourceContentDto;
use App\Enums\CardType;
use App\Pipelines\Flashcard\FlashcardPipelineContext;
use App\Prompts\FlashcardGeneratePrompt;
use Closure;
use Exception;
use Gemini\Data\Schema;
use Gemini\Enums\DataType;
use Illuminate\Support\Collection;

class GenerateFlashcardPipe
{
    public function __construct(
        private readonly GenerateJsonAction $generateJsonAction
    ) {}

    public function handle(FlashcardPipelineContext $context, Closure $next)
    {
        $context->log('GenerateFlashcardPipe started', [
            'sources_count' => $context->sources->count(),
        ]);

        $context->results = $context->sources->flatMap(function (SourceContentDto $source) use ($context) {
            try {
                $context->log('Generating flashcards for source', [
                    'source_title' => $source->title,
                ]);

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
                                ],
                                required: ['type', 'front']
                            )
                        ),
                    ],
                    required: ['flashcards']
                );

                $data = $this->generateJsonAction->execute(
                    FlashcardGeneratePrompt::handle($source),
                    $schema
                );

                if (isset($data->flashcards)) {
                    $dtos = $this->toDto($data->flashcards, $context->title, $source->title);

                    $context->log('Generated flashcards successfully', [
                        'source' => $source->title,
                        'count' => $dtos->count(),
                    ]);

                    return $dtos;
                }

                $context->log('No flashcards found in response for source', [
                    'source' => $source->title,
                ]);

                return collect();
            } catch (Exception $e) {
                $context->log('Error generating flashcards for source', [
                    'source' => $source->title,
                    'error' => $e->getMessage(),
                ]);

                return collect();
            }
        });

        return $next($context);
    }

    private function toDto(array $flashcards, string $title, string $subtitle): Collection
    {
        return collect($flashcards)
            ->map(function ($card) use ($title, $subtitle) {
                $card->deck = "{$title}::$subtitle";

                return match ($card->type) {
                    CardType::CLOZE->value => GeneratedFlashcardDto::omitFromObject($card),
                    CardType::SIMPLE->value => GeneratedFlashcardDto::simpleFromObject($card),
                    default => null,
                };
            })
            ->filter();
    }
}

<?php

namespace App\Pipelines\Flashcard\Pipes;

use App\DTOs\GeneratedFlashcardDto;
use App\DTOs\RawFlashcardDto;
use App\Enums\CardTypes;
use App\Pipelines\Flashcard\FlashcardPipelineContext;
use App\Prompts\FlashcardGeneratePrompt;
use Closure;
use Gemini\Data\GenerationConfig;
use Gemini\Data\Schema;
use Gemini\Enums\DataType;
use Gemini\Enums\ResponseMimeType;
use Gemini\Laravel\Facades\Gemini;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class GenerateFlashcardPipe
{
    public function handle(FlashcardPipelineContext $context, Closure $next)
    {
        $context->results = $context->flashcards->flatMap(function (RawFlashcardDto $flashcard) {
            try {
                $response = Gemini::generativeModel('gemini-2.5-flash')
                    ->withGenerationConfig(
                        generationConfig: new GenerationConfig(
                            responseMimeType: ResponseMimeType::APPLICATION_JSON,
                            responseSchema: new Schema(
                                type: DataType::OBJECT,
                                properties: [
                                    'flashcards' => new Schema(
                                        type: DataType::ARRAY,
                                        items: new Schema(
                                            type: DataType::OBJECT,
                                            properties: [
                                                'type' => new Schema(
                                                    type: DataType::STRING,
                                                    enum: CardTypes::values()
                                                ),
                                                'front' => new Schema(type: DataType::STRING),
                                                'back' => new Schema(type: DataType::STRING),
                                                'extra' => new Schema(type: DataType::STRING),
                                            ],
                                            required: ['type', 'front']
                                        )
                                    )
                                ],
                                required: ['flashcards']
                            )
                        )
                    )
                    ->generateContent(FlashcardGeneratePrompt::handle($flashcard));

                $data = $response->json();

                if (!isset($data->flashcards)) {
                    return collect();
                }

                return $this->toDto($data->flashcards);
            } catch (Exception $e) {
                Log::error($e->getMessage());
                return collect();
            }
        });

        return $next($context);
    }

    private function toDto(array $flashcards): Collection
    {
        return collect($flashcards)
            ->map(function ($card) {
                return match ($card->type) {
                    CardTypes::CARD_OMIT->value => GeneratedFlashcardDto::omitFromObject($card),
                    CardTypes::CARD_SIMPLE->value => GeneratedFlashcardDto::simpleFromObject($card),
                    default => null,
                };
            })
            ->filter();
    }
}

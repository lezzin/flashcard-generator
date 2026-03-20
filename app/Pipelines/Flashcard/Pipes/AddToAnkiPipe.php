<?php

namespace App\Pipelines\Flashcard\Pipes;

use App\Actions\Anki\AddNotesAction;
use App\Actions\Anki\CreateDeckAction;
use App\Actions\Flashcard\Highlight\HighlightNoteAction;
use App\DTOs\GeneratedFlashcardDto;
use App\Enums\CardType;
use App\Pipelines\Flashcard\FlashcardPipelineContext;
use Closure;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AddToAnkiPipe
{
    private const CHUNK_SIZE = 50;

    public function __construct(
        private readonly HighlightNoteAction $highlightNoteAction
    ) {}

    public function handle(FlashcardPipelineContext $context, Closure $next)
    {
        if ($context->results->isEmpty()) {
            return $next($context);
        }

        $payloads = $context->results
            ->map(fn($card) => $this->buildPayload($card));

        $improvedPayloads = $payloads
            ->chunk(self::CHUNK_SIZE)
            ->flatMap(
                fn($chunk) => $this->highlightNoteAction->execute($chunk)
            );

        $improvedPayloads->each(function ($payload) {
            app(CreateDeckAction::class)->execute($payload['deckName']);
        });

        app(AddNotesAction::class)->execute($improvedPayloads->values()->toArray());

        Storage::disk('public')->delete("flashcards/{$context->filename}.json");

        return $next($context);
    }

    private function buildPayload(GeneratedFlashcardDto $flashcard): array
    {
        $fields = [
            'ID' => Str::uuid()->toString(),
            'Extra' => $flashcard->extra,
        ];

        switch ($flashcard->type) {
            case CardType::CLOZE:
                $fields['Texto'] = $flashcard->front;
                break;

            case CardType::SIMPLE:
                $fields['Frente'] = $flashcard->front;
                $fields['Verso'] = $flashcard->back;
                break;
        }

        return [
            'deckName' => $flashcard->deck,
            'modelName' => $flashcard->type->value,
            'tags' => [],
            'fields' => array_filter($fields, fn($v) => ! is_null($v)),
        ];
    }
}

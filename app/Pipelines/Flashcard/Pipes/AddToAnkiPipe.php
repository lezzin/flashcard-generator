<?php

namespace App\Pipelines\Flashcard\Pipes;

use App\Actions\Anki\AddNotesAction;
use App\Actions\Anki\CreateDeckAction;
use App\Actions\Flashcard\Highlight\HighlightNoteAction;
use App\DTOs\GeneratedFlashcardDto;
use App\Enums\CardType;
use App\Pipelines\Flashcard\FlashcardPipelineContext;
use Closure;
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
            $context->log('AddToAnkiPipe skipped: No cards to add');

            return $next($context);
        }

        $context->log('AddToAnkiPipe started', [
            'card_count' => $context->results->count(),
        ]);

        $payloads = $context->results
            ->map(fn ($card) => $this->buildPayload($card));

        $context->log('Highlighting keywords');

        $improvedPayloads = $payloads
            ->chunk(self::CHUNK_SIZE)
            ->flatMap(
                fn ($chunk) => $this->highlightNoteAction->execute($chunk)
            );

        $deckName = $payloads->first()['deckName'] ?? 'Teste';

        $context->log('Adding cards to Anki', [
            'deck' => $deckName,
        ]);

        app(CreateDeckAction::class)->execute($deckName);
        app(AddNotesAction::class)->execute($improvedPayloads->values()->toArray());

        $context->log('Cleaning up temporary file', [
            'filename' => "{$context->filename}.json",
        ]);

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
            'fields' => array_filter($fields, fn ($v) => ! is_null($v)),
        ];
    }
}

<?php

namespace App\Pipelines\Flashcard\Pipes;

use App\Actions\Anki\CreateDeckAction;
use App\Actions\Anki\AddNotesAction;
use App\Actions\Flashcard\HighlightKeywordsAction;
use App\DTOs\GeneratedFlashcardDto;
use App\Enums\CardTypes;
use App\Pipelines\Flashcard\FlashcardPipelineContext;
use Closure;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AddToAnkiPipe
{
    private const CHUNK_SIZE = 50;

    public function __construct(
        private readonly HighlightKeywordsAction $highlightKeywordsAction
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
                fn($chunk) => $this->highlightKeywordsAction->execute($chunk)
            );

        $deckName = $payloads->first()['deckName'] ?? 'Teste';

        app(CreateDeckAction::class)->execute($deckName);
        app(AddNotesAction::class)->execute($improvedPayloads->values()->toArray());

        Storage::disk('public')->delete("flashcards/{$context->filename}.json");

        return $next($context);
    }

    private function buildPayload(GeneratedFlashcardDto $flashcard): array
    {
        $fields = [
            'ID'    => Str::uuid()->toString(),
            'Extra' => $flashcard->extra,
        ];

        switch ($flashcard->type) {
            case CardTypes::CARD_OMIT:
                $fields['Texto'] = $flashcard->front;
                break;

            case CardTypes::CARD_SIMPLE:
                $fields['Frente'] = $flashcard->front;
                $fields['Verso']  = $flashcard->back;
                break;
        }

        return [
            'deckName'  => 'Teste',
            'modelName' => $flashcard->type->value,
            'tags'      => [],
            'fields'    => array_filter($fields, fn($v) => !is_null($v)),
        ];
    }
}

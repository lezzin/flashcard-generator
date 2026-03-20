<?php

namespace App\Actions\Anki;

use App\Enums\CardType;
use App\Jobs\GenerateFlashcardsJob;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateFromDeckAction
{
    private const CHUNK_SIZE = 10;

    public function __construct(
        private readonly FindNotesByDeckNameAction $findNotesAction,
    ) {}

    public function execute(string $deckName): void
    {
        $page = 1;

        do {
            $paginator = $this->findNotesAction->execute($deckName);
            $notes = $paginator->items();

            if (empty($notes)) {
                break;
            }

            $formatted = array_map(function ($note) {
                $type = CardType::tryFrom($note['modelName']);
                $summary = $this->makeSummary($type, $note['fields']);

                return [
                    'title' => $note['deckNames'][0],
                    'summary' => $summary,
                ];
            }, $notes);

            $chunks = array_chunk($formatted, self::CHUNK_SIZE);

            foreach ($chunks as $chunk) {
                $filename = 'uploads/' . Str::random(40) . '.json';

                Storage::put($filename, json_encode($chunk));

                dispatch(new GenerateFlashcardsJob(
                    content: $filename,
                    isPath: true,
                ))->onQueue('flashcard:generate');
            }

            $hasMore = $paginator->hasMorePages();
            $page++;
        } while ($hasMore);
    }

    private function makeSummary(CardType|null $type, array $fields): string
    {
        return match ($type) {
            CardType::SIMPLE => sprintf(
                "Pergunta: %s\nResposta: %s",
                trim($fields['Frente'] ?? ''),
                trim($fields['Verso'] ?? '')
            ),

            CardType::CLOZE => sprintf(
                "Cloze: %s",
                trim($fields['Texto'] ?? '')
            ),

            default => 'Resumo indisponível',
        };
    }
}

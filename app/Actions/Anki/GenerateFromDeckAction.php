<?php

namespace App\Actions\Anki;

use App\Enums\CardType;
use App\Jobs\GenerateFlashcardsJob;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateFromDeckAction
{
    private const CHUNK_SIZE = 20;

    public function __construct(
        private readonly FindNotesByDeckNameAction $findNotesAction,
    ) {}

    public function execute(string $deckName): void
    {
        $page = 1;

        do {
            $paginator = $this->findNotesAction->execute($deckName, page: $page);
            $notes = $paginator->items();

            if (empty($notes)) {
                break;
            }

            $chunks = array_chunk($notes, self::CHUNK_SIZE);

            foreach ($chunks as $chunk) {
                $notesSummary = array_map(function ($note) {
                    $type = CardType::tryFrom($note['modelName']);
                    return $this->makeSummary($type, $note['fields']);
                }, $chunk);

                $formatted = [
                    [
                        'title' => $deckName,
                        'summary' => implode("\n---\n", $notesSummary),
                    ]
                ];

                $filename = 'uploads/' . Str::random(40) . '.json';

                Storage::put($filename, json_encode($formatted));

                dispatch(new GenerateFlashcardsJob(
                    content: $filename,
                    isPath: true,
                    fromDeck: true,
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

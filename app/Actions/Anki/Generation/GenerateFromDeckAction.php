<?php

namespace App\Actions\Anki\Generation;

use App\Actions\Anki\Notes\FindNotesByDeckNameAction;
use App\Enums\CardType;
use App\Jobs\GenerateFlashcardsJob;
use App\Models\BaseContentTree;
use App\Models\GeneratedContent;

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

            $documentTreeId = BaseContentTree::create([
                'data' => json_encode($notes)
            ])->id;

            $chunks = array_chunk($notes, self::CHUNK_SIZE);

            foreach ($chunks as $chunk) {
                $notesSummary = array_map(function ($note) {
                    $type = CardType::tryFrom($note['modelName']);
                    return $this->makeSummary($type, $note['fields']);
                }, $chunk);

                $formatted[] =                     [
                    'title' => $deckName,
                    'description' => implode("\n---\n", $notesSummary),
                    'tree_id' => $documentTreeId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            GeneratedContent::insert($formatted);

            dispatch(new GenerateFlashcardsJob(
                treeId: $documentTreeId,
                title: $deckName,
                fromDeck: true,
            ))->onQueue('flashcard:generate');

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

<?php

namespace App\Actions\Anki;

use App\Actions\Anki\Api\FindNotesByDeckNameAction;
use App\Enums\CardType;
use App\Jobs\Flashcard\FlashcardPipelineJob;
use App\Models\BaseContentTree;
use App\Models\GeneratedContent;
use Illuminate\Support\Facades\Log;

class GenerateFlashcardFromDeckAction
{
    private const CHUNK_SIZE = 20;

    public function __construct(
        private readonly FindNotesByDeckNameAction $findNotesAction,
    ) {}

    public function execute(string $deckName): void
    {
        $page = 1;

        do {
            $paginator = $this->findNotesAction->execute(deckName: $deckName, page: $page);
            $notes = $paginator->items();

            if (count($notes) === 0) {
                break;
            }

            $documentTreeId = BaseContentTree::create([
                'data' => json_encode($notes)
            ])->id;

            $chunks = array_chunk($notes, self::CHUNK_SIZE);

            foreach ($chunks as $chunk) {
                $notesSummary = array_map(function ($note) {
                    $type = CardType::tryFrom($note['modelName']);

                    if ($type === null) {
                        Log::channel('flashcard')->warning("Card type not mapped on application.", [
                            'type' => $type,
                            'note' => json_encode($note),
                            'method' => 'GenerateFlashcardFromDeckAction::makeSummary'
                        ]);
                    }

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

            dispatch(new FlashcardPipelineJob(
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

<?php

namespace App\Actions\Anki\Api;

use App\Actions\Anki\BaseFlashcardHighlightAction;
use App\DTOs\Anki\NoteDto;
use App\Services\Anki\AnkiConnectClient;
use Illuminate\Pagination\LengthAwarePaginator;

class FindNotesByDeckNameAction
{
    private const CHUNK_SIZE = 200;
    private const TARGET_FIELDS = ['Frente', 'Verso', 'Texto'];

    public function __construct(
        private readonly AnkiConnectClient $ankiClient,
        private readonly GetDeckNamesFromCardIdsAction $getDeckNames,
    ) {}

    public function execute(
        string $deckName,
        int $perPage = 100,
        int $page = 1,
        bool $stripTags = true,
        bool $filterByStyle = true,
    ): LengthAwarePaginator {
        $noteIds = $this->ankiClient->invoke('findNotes', [
            'query' => "\"deck:{$deckName}\"",
        ]);

        $results = $filterByStyle
            ? $this->getFilteredNotes($noteIds, $stripTags)
            : $this->getPaginatedNotes($noteIds, $perPage, $page, $stripTags);

        return new LengthAwarePaginator(
            $results['items'],
            $results['total'],
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    private function getFilteredNotes(array $noteIds, bool $stripTags): array
    {
        $filtered = collect($noteIds)
            ->chunk(self::CHUNK_SIZE)
            ->flatMap(function ($chunk) {
                return $this->ankiClient->invoke('notesInfo', [
                    'notes' => $chunk->map(fn($id) => (int) $id)->values()->all()
                ]);
            })
            ->reject(fn($note) => $this->noteHasColor($note));

        $total = $filtered->count();

        $pagedItems = $filtered->forPage(request('page', 1), request('perPage', 100))
            ->map(fn($note) => $this->enrichAndFormatNote($note, $stripTags))
            ->values();

        return ['items' => $pagedItems, 'total' => $total];
    }

    private function getPaginatedNotes(array $noteIds, int $perPage, int $page, bool $stripTags): array
    {
        $offset = ($page - 1) * $perPage;
        $chunkIds = array_slice($noteIds, $offset, $perPage);

        $noteInfos = $this->ankiClient->invoke('notesInfo', ['notes' => $chunkIds]);

        $items = collect($noteInfos)
            ->map(fn($note) => $this->enrichAndFormatNote($note, $stripTags));

        return ['items' => $items, 'total' => count($noteIds)];
    }

    private function enrichAndFormatNote(array $note, bool $stripTags): array
    {
        $deckNames = $this->getDeckNames->execute($note['cards'] ?? []);

        return NoteDto::fromRequest($note)
            ->withDeckNames($deckNames)
            ->toArray($stripTags);
    }

    private function noteHasColor(array $note): bool
    {
        $colors = BaseFlashcardHighlightAction::COLORS;

        return collect($note['fields'] ?? [])
            ->only(self::TARGET_FIELDS)
            ->contains(function ($fieldData) use ($colors) {
                $content = $fieldData['value'] ?? '';

                foreach ($colors as $color) {
                    if (str_contains($content, $color)) {
                        return true;
                    }
                }

                return false;
            });
    }
}

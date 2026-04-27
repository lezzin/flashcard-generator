<?php

namespace App\Actions\Anki;

use App\Actions\Gemini\GenerateJsonAction;
use App\Enums\CardType;
use App\Models\AnkiNote;
use App\Prompts\FlashcardEnhancePrompt;
use App\Support\AnkiFieldNormalizer;
use Illuminate\Support\Collection;

class HighlightNoteAction extends BaseFlashcardHighlightAction
{
    public function __construct(
        private readonly GenerateJsonAction $generateJsonAction
    ) {}

    public function execute(array|Collection $notes): array|Collection
    {
        $isCollection = $notes instanceof Collection;
        $notes = collect($isCollection ? $notes : [$notes])->values();

        $notesWithHashes = $this->mapWithHashes($notes);
        $notesWithHashes = $notesWithHashes->unique('hash')->values();

        $cached = $this->getCached($notesWithHashes);
        $this->enhanceMissing($notesWithHashes, $cached);

        $cached = $this->getCached($notesWithHashes);
        $result = $this->buildResult($notesWithHashes, $cached);

        return $isCollection ? $result : $result->first();
    }

    protected function mapWithHashes(Collection $notes): Collection
    {
        return $notes->map(fn($note) => [
            'original' => $note,
            'hash' => $this->getNoteHash($note),
        ]);
    }

    protected function getCached(Collection $notes): Collection
    {
        return AnkiNote::whereIn(
            'fields_hash',
            $notes->pluck('hash')
        )->get()->keyBy('fields_hash');
    }

    protected function enhanceMissing(Collection $notes, Collection $cached): void
    {
        $missing = $notes->reject(
            fn($n) => $cached->has($n['hash'])
        )->values();

        if ($missing->isEmpty()) {
            return;
        }

        $payloads = $this->buildPayloads($missing->pluck('original'));

        if (empty($payloads)) {
            return;
        }

        $enhanced = $this->generateJsonAction->execute(
            FlashcardEnhancePrompt::handle($payloads),
            FlashcardEnhancePrompt::schema(),
        );

        $aiResults = $enhanced->results ?? [];

        foreach ($missing as $index => $item) {
            $this->storeEnhanced(
                item: $item,
                payload: $payloads[$index] ?? [],
                ai: $aiResults[$index] ?? null
            );
        }
    }

    protected function storeEnhanced(array $item, array $payload, ?object $ai): void
    {
        if (!$ai) {
            return;
        }

        AnkiNote::updateOrCreate(
            ['fields_hash' => $item['hash']],
            [
                'anki_id' => $item['original']['noteId'] ?? null,
                'model_name' => $item['original']['modelName'],
                'type' => ($payload['type'] ?? null) === 'qa'
                    ? CardType::SIMPLE
                    : CardType::CLOZE,
                'fields' => $item['original']['fields'],
                'improved_fields' => $this->getImprovedFieldsFromAI($item['original'], $ai),
                'keywords' => $ai->keywords ?? [],
                'is_valid' => $ai->valid ?? null,
                'is_recoverable' => $ai->recoverable ?? null,
                'invalidation_reason' => $ai->reason ?? null,
            ]
        );
    }

    protected function buildResult(Collection $notes, Collection $cached): Collection
    {
        return $notes->map(function ($item) use ($cached) {
            $note = $item['original'];
            $cache = $cached->get($item['hash']);

            if (!$cache) {
                return $note;
            }

            $note['ai'] = [
                'valid' => $cache->is_valid,
                'recoverable' => $cache->is_recoverable,
                'reason' => $cache->invalidation_reason,
            ];

            if (!$cache->is_recoverable) {
                $note['invalid'] = true;
                return $note;
            }

            if (!empty($cache->improved_fields)) {
                $note['fields'] = array_merge(
                    $note['fields'],
                    $cache->improved_fields
                );
            }

            return $this->applyStylingToFields(
                $note,
                $cache->keywords ?? []
            );
        });
    }

    protected function getImprovedFieldsFromAI(array $note, object $ai): array
    {
        $text = trim($ai->improved_text ?? '');

        if ($text === '') {
            return [];
        }

        $temp = $this->applyImprovedText($note, $text);

        if (property_exists($ai, 'extra')) {
            $temp['fields']['Extra'] = $ai->extra;
        }

        $new = AnkiFieldNormalizer::normalizeExtra($temp['fields']);
        $old = AnkiFieldNormalizer::normalizeExtra($note['fields']);

        return array_diff_assoc($new, $old);
    }
}

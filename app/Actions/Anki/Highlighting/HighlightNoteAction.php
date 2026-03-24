<?php

namespace App\Actions\Anki\Highlighting;

use App\Actions\Gemini\GenerateJsonAction;
use App\Enums\CardType;
use App\Models\AnkiNote;
use App\Prompts\FlashcardEnhancePrompt;
use Gemini\Data\Schema;
use Gemini\Enums\DataType;
use Illuminate\Support\Collection;

class HighlightNoteAction extends BaseHighlightAction
{
    public function __construct(
        private readonly GenerateJsonAction $generateJsonAction
    ) {}

    public function execute(array|Collection $notes): array|Collection
    {
        $isCollection = $notes instanceof Collection;
        $notesCollection = ($isCollection ? $notes : collect([$notes]))->values();

        $notesWithHashes = $notesCollection->map(function ($note) {
            return [
                'original' => $note,
                'hash' => $this->getNoteHash($note),
            ];
        });

        $hashes = $notesWithHashes->pluck('hash')->unique()->toArray();
        $cachedResults = AnkiNote::whereIn('fields_hash', $hashes)->get()->keyBy('fields_hash');

        $toEnhance = $notesWithHashes->filter(function ($item) use ($cachedResults) {
            return !$cachedResults->has($item['hash']);
        });

        if ($toEnhance->isNotEmpty()) {
            $uniqueToEnhance = $toEnhance->unique('hash');
            $payloads = $this->buildPayloads($uniqueToEnhance->pluck('original'));

            if (!empty($payloads)) {
                $aiResults = $this->enhance($payloads);

                foreach ($uniqueToEnhance->values() as $index => $item) {
                    $ai = $aiResults[$index] ?? null;
                    if (!$ai) continue;

                    AnkiNote::updateOrCreate(
                        ['fields_hash' => $item['hash']],
                        [
                            'anki_id' => $item['original']['noteId'] ?? null,
                            'model_name' => $item['original']['modelName'],
                            'type' => ($payloads[$index]['type'] ?? 'unknown') == 'qa' ? CardType::SIMPLE : CardType::CLOZE,
                            'fields' => $item['original']['fields'],
                            'improved_fields' => $this->getImprovedFieldsFromAI($item['original'], $ai),
                            'keywords' => $ai->keywords ?? [],
                            'is_valid' => $ai->valid ?? null,
                            'is_recoverable' => $ai->recoverable ?? null,
                            'invalidation_reason' => $ai->reason ?? null,
                        ]
                    );
                }

                $cachedResults = AnkiNote::whereIn('fields_hash', $hashes)->get()->keyBy('fields_hash');
            }
        }

        $results = $notesWithHashes->map(function ($item) use ($cachedResults) {
            $note = $item['original'];
            $cached = $cachedResults->get($item['hash']);

            if (!$cached) {
                return $note;
            }

            $note['ai'] = [
                'valid' => $cached->is_valid,
                'recoverable' => $cached->is_recoverable,
                'reason' => $cached->invalidation_reason,
            ];

            if (!$cached->is_recoverable) {
                $note['invalid'] = true;
                return $note;
            }

            if (!empty($cached->improved_fields)) {
                $note['fields'] = array_merge($note['fields'], $cached->improved_fields);
            }

            return $this->applyStylingToFields($note, $cached->keywords ?? []);
        });

        return $isCollection ? $results : $results->first();
    }

    protected function getImprovedFieldsFromAI(array $note, object $ai): array
    {
        $improvedText = trim($ai->improved_text ?? '');
        if (empty($improvedText)) {
            return [];
        }

        $tempNote = $this->applyImprovedText($note, $improvedText);

        return array_diff_assoc($tempNote['fields'], $note['fields']);
    }

    protected function enhance(array $payloads): array
    {
        if (empty($payloads)) {
            return [];
        }

        $schema = new Schema(
            type: DataType::OBJECT,
            properties: [
                'results' => new Schema(
                    type: DataType::ARRAY,
                    items: new Schema(
                        type: DataType::OBJECT,
                        properties: [
                            'valid' => new Schema(type: DataType::BOOLEAN),
                            'recoverable' => new Schema(type: DataType::BOOLEAN),
                            'reason' => new Schema(type: DataType::STRING),
                            'improved_text' => new Schema(type: DataType::STRING),
                            'keywords' => new Schema(
                                type: DataType::ARRAY,
                                items: new Schema(type: DataType::STRING),
                                minItems: 0,
                                maxItems: 3
                            ),
                        ],
                        required: ['valid', 'recoverable', 'reason', 'improved_text', 'keywords']
                    )
                ),
            ],
            required: ['results']
        );

        $data = $this->generateJsonAction->execute(
            FlashcardEnhancePrompt::handle($payloads),
            $schema
        );

        return $data->results ?? [];
    }
}

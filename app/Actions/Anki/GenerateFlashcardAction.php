<?php

namespace App\Actions\Anki;

use App\Actions\Gemini\GenerateJsonAction;
use App\DTOs\SourceContentDto;
use App\Helpers\Log as HelpersLog;
use App\Mappers\FlashcardMapper;
use App\Prompts\FlashcardGeneratePrompt;
use Illuminate\Support\Facades\Log;

use function Illuminate\Support\now;

class GenerateFlashcardAction
{
    public function __construct(
        protected readonly GenerateJsonAction $generateJsonAction
    ) {}

    public function execute(SourceContentDto $source)
    {
        app(AddFromAIToAnkiAction::class)->execute(
            $this->getFlashcards($source)
        );
    }

    protected function getFlashcards(SourceContentDto $source)
    {
        if ($source->isBackup) {
            $encoded = HelpersLog::getJson('gemini-backup.log', 'FLASHCARD GENERATED');

            if (!$encoded) {
                return collect();
            }

            $backup = json_decode(
                json_decode($encoded)->data
            )->flashcards ?? [];

            return collect($backup)
                ->map(fn($card) => FlashcardMapper::toDto($card, $source->title))
                ->filter();
        }

        $data = $this->generateJsonAction->execute(
            FlashcardGeneratePrompt::handle($source),
            FlashcardGeneratePrompt::schema()
        );

        if (!isset($data->flashcards)) {
            Log::channel('content')->info('The generated data for flashcards is empty: ', [
                'content' => json_encode($source),
                'data'    => json_encode($data),
            ]);

            return collect();
        }

        Log::channel('gemini-backup')->info("[FLASHCARD GENERATED]", [
            'data' => json_encode($data),
            'timestamp' => now(),
        ]);

        return collect($data->flashcards)
            ->map(fn($card) => FlashcardMapper::toDto($card, $source->title))
            ->filter();
    }
}

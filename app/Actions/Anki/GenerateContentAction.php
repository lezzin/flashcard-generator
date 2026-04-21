<?php

namespace App\Actions\Anki;

use App\Actions\Gemini\GenerateJsonAction;
use App\DTOs\SourceContentDto;
use App\Models\GeneratedContent;
use App\Prompts\ContentGeneratePrompt;
use Illuminate\Support\Facades\Log;

class GenerateContentAction
{
    public function __construct(
        private readonly GenerateJsonAction $generateJsonAction
    ) {
    }

    public function execute(string $content)
    {
        $result = $this->generateJsonAction->execute(
            ContentGeneratePrompt::handle($content),
            ContentGeneratePrompt::schema()
        );

        Log::channel('gemini-backup')->info("[CONTENT GENERATED]", [
            'data' => json_encode($result),
            'timestamp' => now(),
        ]);

        GeneratedContent::insert([
            'title'       => $result->title,
            'description' => $result->content,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $source = SourceContentDto::fromAIResult($result);
        app(GenerateFlashcardAction::class)->execute($source);

        return $result;
    }
}

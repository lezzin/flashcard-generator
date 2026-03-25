<?php

namespace App\Actions\Anki;

use App\Actions\Gemini\GenerateJsonAction;
use App\Models\GeneratedContent;
use App\Prompts\ContentGeneratePrompt;
use Gemini\Data\Schema;
use Gemini\Enums\DataType;
use Illuminate\Support\Facades\Log;

class GenerateContentAction
{
    public function __construct(
        private readonly GenerateJsonAction $generateJsonAction
    ) {}

    public function execute(array $chunk, int $documentTreeId, ?string $newContext = null)
    {
        if (count($chunk) === 0) {
            Log::channel('content')->info('The provided content chunk is empty: ', [
                'content' => json_encode($chunk),
                'tree_id' => $documentTreeId,
                'context' => $newContext
            ]);

            return;
        }

        $schema = new Schema(
            type: DataType::OBJECT,
            properties: [
                'title' => new Schema(type: DataType::STRING),
                'summary' => new Schema(type: DataType::STRING),
            ],
            required: ['title', 'summary'],
        );

        $prompt = ContentGeneratePrompt::handle(
            $newContext,
            implode("\n", $chunk)
        );

        $result = $this->generateJsonAction->execute($prompt, $schema);

        GeneratedContent::insert([
            'title'       => $result->title,
            'description' => $result->summary,
            'tree_id'     => $documentTreeId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

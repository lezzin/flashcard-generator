<?php

namespace App\Actions\Anki\Generation;

use App\Actions\Gemini\GenerateJsonAction;
use App\Models\GeneratedContent;
use App\Prompts\ContentGeneratePrompt;
use Exception;
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
        $schema = new Schema(
            type: DataType::OBJECT,
            properties: [
                'title' => new Schema(type: DataType::STRING),
                'summary' => new Schema(type: DataType::STRING),
            ],
            required: ['title', 'summary'],
        );

        try {
            $result = $this->generateJsonAction->execute(
                ContentGeneratePrompt::handle(
                    $newContext,
                    implode("\n", $chunk)
                ),
                $schema
            );

            GeneratedContent::insert([
                'title'       => $result->title,
                'description' => $result->summary,
                'tree_id'     => $documentTreeId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (Exception $e) {
            Log::channel('content')->error(
                "Failed to generate content: " . $e->getMessage()
            );

            throw $e;
        }
    }
}

<?php

namespace App\Actions\Gemini;

use Exception;
use Gemini\Data\GenerationConfig;
use Gemini\Data\Schema;
use Gemini\Enums\ResponseMimeType;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;

class GenerateJsonAction
{
    public function execute(
        string $prompt,
        ?Schema $schema = null,
        ?float $temperature = null,
        ?int $maxOutputTokens = null
    ): mixed {
        try {
            $config = [
                'responseMimeType' => $schema ? ResponseMimeType::APPLICATION_JSON : ResponseMimeType::TEXT_PLAIN,
            ];

            if ($schema) {
                $config['responseSchema'] = $schema;
            }

            if ($temperature !== null) {
                $config['temperature'] = $temperature;
            }

            if ($maxOutputTokens !== null) {
                $config['maxOutputTokens'] = $maxOutputTokens;
            }

            $response = Gemini::generativeModel(config('gemini.model'))
                ->withGenerationConfig(
                    generationConfig: new GenerationConfig(...$config)
                )
                ->generateContent($prompt);

            return $schema ? $response->json() : $response->text();
        } catch (Exception $e) {
            Log::error('Gemini Generation Error: '.$e->getMessage(), [
                'prompt' => $prompt,
                'exception' => $e,
            ]);

            throw $e;
        }
    }
}

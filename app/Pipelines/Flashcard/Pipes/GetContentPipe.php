<?php

namespace App\Pipelines\Flashcard\Pipes;

use App\DTOs\SourceContentDto;
use App\Pipelines\Flashcard\FlashcardPipelineContext;
use Closure;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GetContentPipe
{
    public function handle(FlashcardPipelineContext $context, Closure $next)
    {
        if ($context->isPath) {
            $this->handleFile($context);
        } else {
            $this->handleText($context);
        }

        return $next($context);
    }

    private function handleFile(FlashcardPipelineContext $context): void
    {
        if (!Storage::exists($context->content)) {
            return;
        }

        $raw = Storage::get($context->content);

        $decoded = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            return;
        }

        foreach ($decoded as $item) {
            if (!isset($item['title'], $item['summary'])) {
                continue;
            }

            $context->sources->add(new SourceContentDto(
                title: $item['title'],
                content: $item['summary'],
            ));
        }
    }

    private function handleText(FlashcardPipelineContext $context): void
    {
        $rawSummaries = explode('{{FIM_RESUMO}}', trim($context->content));

        foreach ($rawSummaries as $raw) {
            $parsed = $this->parseSummary($raw, $context);

            if (!$parsed) {
                continue;
            }

            [$title, $body] = $parsed;

            $context->sources->add(new SourceContentDto(
                title: $title,
                content: $body,
            ));
        }
    }

    private function parseSummary(string $raw, FlashcardPipelineContext $context): ?array
    {
        $clean = trim(str_replace('{{INICIO_RESUMO}}', '', $raw));

        if ($clean === '') {
            return null;
        }

        $parts = explode('{{FIM_TITULO_RESUMO}}', $clean, 2);

        if (count($parts) < 2) {
            return null;
        }

        $title = trim(Str::replace('{{TITULO_RESUMO}}', '', $parts[0]));
        $body = trim($parts[1]);

        if ($title === '' || $body === '') {
            return null;
        }

        return [$title, $body];
    }
}

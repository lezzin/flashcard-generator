<?php

namespace App\Pipelines\Summary\Pipes;

use App\Pipelines\Summary\SummaryPipelineContext;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class StructureStudyContentPipe
{
    private const MAX_BLOCK_CHARS = 6000;

    private const MIN_BLOCK_CHARS = 2000;

    public function handle(SummaryPipelineContext $context, Closure $next)
    {
        $context->blocks = $this->extractBlocks($context->content);
        $context->cleanContent();

        return $next($context);
    }

    private function extractBlocks(string $text): Collection
    {
        $lines = collect(explode("\n", $text))
            ->map(fn ($line) => trim($line))
            ->filter();

        $blocks = collect();
        $currentContent = [];
        $currentTitle = null;

        foreach ($lines as $line) {
            if ($this->isHeading($line)) {
                if (! empty($currentContent)) {
                    $blocks->push([
                        'title' => $currentTitle ?? 'Introdução',
                        'content' => implode("\n", $currentContent),
                    ]);
                }
                $currentTitle = $line;
                $currentContent = [];

                continue;
            }
            $currentContent[] = $line;
        }

        if (! empty($currentContent)) {
            $blocks->push([
                'title' => $currentTitle ?? 'Introdução',
                'content' => implode("\n", $currentContent),
            ]);
        }

        if ($blocks->isEmpty() && ! empty($text)) {
            $blocks->push([
                'title' => 'Conteúdo Principal',
                'content' => $text,
            ]);
        }

        $blocks = $this->groupSmallBlocks($blocks);

        return $this->chunkLargeBlocks($blocks);
    }

    private function isHeading(string $line): bool
    {
        $line = trim($line);

        if (strlen($line) < 4 || strlen($line) > 100) {
            return false;
        }

        return
            preg_match('/^\d+(\.\d+)*\.?\s+[A-Z]/', $line) ||
            (Str::upper($line) === $line && strlen($line) > 10) ||
            (Str::endsWith($line, ':') && strlen($line) < 60);
    }

    private function groupSmallBlocks(Collection $blocks): Collection
    {
        $grouped = collect();
        $temp = null;

        foreach ($blocks as $block) {
            if ($temp === null) {
                $temp = $block;

                continue;
            }

            if (strlen($temp['content']) + strlen($block['content']) < self::MIN_BLOCK_CHARS) {
                $temp['content'] .= "\n\n".$block['content'];
                if ($block['title'] && $block['title'] !== 'Introdução') {
                    $temp['title'] .= ' & '.$block['title'];
                }
            } else {
                $grouped->push($temp);
                $temp = $block;
            }
        }

        if ($temp) {
            $grouped->push($temp);
        }

        return $grouped;
    }

    private function chunkLargeBlocks(Collection $blocks): Collection
    {
        $chunkedBlocks = collect();

        foreach ($blocks as $block) {
            if (strlen($block['content']) > self::MAX_BLOCK_CHARS) {
                $chunks = str_split($block['content'], self::MAX_BLOCK_CHARS);
                foreach ($chunks as $index => $chunk) {
                    $title = $block['title'] ?? 'Continuação';
                    $chunkedBlocks->push([
                        'title' => $title.' (Parte '.($index + 1).')',
                        'content' => $chunk,
                    ]);
                }
            } else {
                $chunkedBlocks->push($block);
            }
        }

        return $chunkedBlocks;
    }
}

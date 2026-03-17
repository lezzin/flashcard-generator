<?php

namespace App\Actions\Flashcard;

use Illuminate\Support\Collection;

class HighlightManyAction extends BaseHighlightAction
{
    public function execute(Collection $notes): Collection
    {
        $texts = $this->extractTexts($notes);
        $keywordsList = $this->getKeywords($texts);

        return $notes->values()->map(function ($note, $index) use ($keywordsList) {
            $keywords = $keywordsList[$index]->keywords ?? [];

            return $this->applyStylingToFields($note, $keywords);
        });
    }

    private function extractTexts(Collection $notes): array
    {
        return $notes
            ->map(
                fn ($note) => $this->extractText($note)
            )
            ->filter()
            ->values()
            ->toArray();
    }
}

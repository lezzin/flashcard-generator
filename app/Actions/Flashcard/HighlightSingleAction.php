<?php

namespace App\Actions\Flashcard;

class HighlightSingleAction extends BaseHighlightAction
{
    public function execute(array $note): array
    {
        $text = $this->extractText($note);

        if (! $text) {
            return $note;
        }

        $keywordsList = $this->getKeywords([$text]);
        $keywords = $keywordsList[0]->keywords ?? [];

        return $this->applyStylingToFields($note, $keywords);
    }
}

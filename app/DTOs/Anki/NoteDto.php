<?php

namespace App\DTOs\Anki;

class NoteDto
{
    public function __construct(
        public readonly int $noteId,
        public readonly string $profile,
        public readonly array $fields,
        public readonly string $modelName,
        public readonly int $mod,
        public readonly ?array $tags,
        public readonly ?array $cards,
    ) {}

    public static function fromRequest(array $request): self
    {
        return new self(
            noteId: $request['noteId'],
            profile: $request['profile'],
            fields: $request['fields'],
            modelName: $request['modelName'],
            mod: $request['mod'],
            tags: $request['tags'],
            cards: $request['cards'],
        );
    }

    public function toArray(): array
    {
        $formattedFields = collect($this->fields)
            ->mapWithKeys(fn ($field, $name) => [$name => strip_tags($field['value'])])
            ->all();

        return [
            'noteId' => $this->noteId,
            'modelName' => $this->modelName,
            'fields' => $formattedFields,
        ];
    }
}

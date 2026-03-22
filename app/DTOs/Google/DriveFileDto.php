<?php

namespace App\DTOs\Google;

use App\Helpers\Date;
use Illuminate\Support\Carbon;

class DriveFileDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $type,
        public readonly string $createdTime,
        public readonly ?string $mimeType,
        public readonly ?string $url,
        public readonly ?string $downloadUrl,
        public array $children = []
    ) {}

    public static function folder(object $folder): self
    {
        return new self(
            id: $folder->id,
            name: $folder->name,
            type: 'folder',
            mimeType: $folder->mimeType,
            url: $folder->webViewLink ?? null,
            downloadUrl: null,
            createdTime: $folder->createdTime,
            children: []
        );
    }

    public static function file(object $file): self
    {
        return new self(
            id: $file->id,
            name: $file->name,
            type: 'file',
            mimeType: $file->mimeType,
            url: $file->webViewLink ?? null,
            downloadUrl: route('files.download', $file->id),
            createdTime: $file->createdTime,
            children: []
        );
    }

    public function addChild(self $child): void
    {
        $this->children[] = $child;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'mimeType' => $this->mimeType,
            'url' => $this->url,
            'downloadUrl' => $this->downloadUrl,
            'createdTime' => Date::toTimezone($this->createdTime),
            'children' => $this->sortChildren()
        ];
    }

    private function sortChildren()
    {
        return collect($this->children)
            ->sortBy([
                fn($item) => $item->type !== 'folder',
                fn($item) => strtolower($item->name),
            ])
            ->map(fn($c) => $c->toArray())
            ->values()
            ->all();
    }
}

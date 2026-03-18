<?php

namespace App\DTOs\Google;

class DriveFileDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $type,
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
            'children' => array_map(fn($c) => $c->toArray(), $this->children),
        ];
    }
}

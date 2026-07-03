<?php

namespace Exxxar\Kanban\DTO;

use Carbon\Carbon;

class AttachmentDto
{
    public function __construct(
        public int $id,
        public string $name,
        public string $path,
        public string $url,
        public ?string $mime,
        public ?int $size,
        public ?Carbon $created_at,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? 0,
            name: $data['name'] ?? '',
            path: $data['path'] ?? '',
            url: $data['url'] ?? '',
            mime: $data['mime'] ?? null,
            size: $data['size'] ?? null,
            created_at: isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
        );
    }

    /**
     * @return self[]
     */
    public static function collection(array $items): array
    {
        return array_map(fn($item) => self::fromArray($item), $items);
    }

    public function isImage(): bool
    {
        return $this->mime && str_starts_with($this->mime, 'image/');
    }

    public function isPdf(): bool
    {
        return $this->mime === 'application/pdf' || str_ends_with($this->name, '.pdf');
    }

    public function getSizeFormatted(): string
    {
        if (!$this->size) return '0 B';

        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        $size = $this->size;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return round($size, 2) . ' ' . $units[$i];
    }
}
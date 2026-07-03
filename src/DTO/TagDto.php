<?php

namespace Exxxar\Kanban\DTO;

class TagDto
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $color,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? 0,
            name: $data['name'] ?? '',
            color: $data['color'] ?? null,
        );
    }

    /**
     * @return self[]
     */
    public static function collection(array $items): array
    {
        return array_map(fn($item) => self::fromArray($item), $items);
    }
}
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
            id: $data['id'],
            name: $data['name'],
            color: $data['color'] ?? null,
        );
    }
}
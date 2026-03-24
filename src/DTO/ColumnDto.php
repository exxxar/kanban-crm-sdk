<?php

namespace Exxxar\Kanban\DTO;

class ColumnDto
{
    public function __construct(
        public int $id,
        public int $board_id,
        public string $title,
        public int $position,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            board_id: $data['board_id'],
            title: $data['title'],
            position: $data['position'] ?? 0,
        );
    }
}
<?php

namespace Exxxar\Kanban\DTO;

class ColumnDto
{
    public function __construct(
        public int $id,
        public int $board_id,
        public int $thread,
        public string $title,
        public int $position,
        public ?array $notifications = null,
        public int $tasks_count = 0,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? 0,
            board_id: $data['board_id'] ?? 0,
            thread: $data['thread'] ?? 0,
            title: $data['title'] ?? '',
            position: $data['position'] ?? 0,
            notifications: $data['notifications'] ?? null,
            tasks_count: $data['tasks_count'] ?? 0,
        );
    }
}
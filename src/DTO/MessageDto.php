<?php

namespace Exxxar\Kanban\DTO;


use Carbon\Carbon;

class MessageDto
{
    public function __construct(
        public int $id,
        public int $task_id,
        public string $message,
        public ?Carbon $created_at,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            task_id: $data['task_id'],
            message: $data['message'],
            created_at: isset($data['created_at'])
                ? Carbon::parse($data['created_at'])
                : null,
        );
    }
}
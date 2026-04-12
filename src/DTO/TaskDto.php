<?php

namespace Exxxar\Kanban\DTO;

use Carbon\Carbon;

class TaskDto
{
    public function __construct(
        public int $id,
        public ?int $column_id,
        public string $title,
        public ?string $description,
        public ?int $priority,
        public ?Carbon $due_date,
        public ?Carbon $last_viewed_at,
        public array $labels,
        public array $attachments,
        public array $subtasks,
        public int $type,
        public array $data,
        public int $position,

        /** @var TagDto[] */
        public array $tags,

        /** @var MessageDto[] */
        public array $messages,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id']?? null,
            column_id: $data['column_id'] ?? null,
            title: $data['title'],
            description: $data['description'] ?? null,
            priority: $data['priority'] ?? null,

            due_date: isset($data['due_date'])
                ? Carbon::parse($data['due_date'])
                : null,

            last_viewed_at: isset($data['last_viewed_at'])
                ? Carbon::parse($data['last_viewed_at'])
                : null,

            labels: $data['labels'] ?? [],
            attachments: $data['attachments'] ?? [],
            subtasks: $data['subtasks'] ?? [],
            type: $data['type'],
            data: $data['data'] ?? [],
            position: $data['position'] ?? 0,

            tags: array_map(
                fn ($tag) => TagDto::fromArray($tag),
                $data['tags'] ?? []
            ),

            messages: array_map(
                fn ($msg) => MessageDto::fromArray($msg),
                $data['messages'] ?? []
            ),
        );
    }
}
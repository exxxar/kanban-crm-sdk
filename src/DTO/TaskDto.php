<?php

namespace Exxxar\Kanban\DTO;

use Carbon\Carbon;
use Exxxar\Kanban\Enums\TaskTypeEnum;
use Exxxar\Kanban\Enums\PriorityEnum;

class TaskDto
{
    public function __construct(
        public int $id,
        public ?int $column_id,
        public ?int $board_id,
        public string $title,
        public ?string $description,
        public ?PriorityEnum $priority,
        public TaskTypeEnum $type,
        public ?Carbon $due_date,
        public ?Carbon $created_at,
        public ?Carbon $updated_at,
        public ?Carbon $last_viewed_at,
        public array $labels,
        public array $subtasks,
        public array $custom_data,
        public int $position,
        public int $comments_count,
        /** @var TagDto[] */
        public array $tags,
        /** @var AttachmentDto[] */
        public array $attachments,
        /** @var MessageDto[] */
        public array $messages,
        /** @var ClientDto|null */
        public ?ClientDto $client,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? 0,
            column_id: $data['column_id'] ?? null,
            board_id: $data['board_id'] ?? null,
            title: $data['title'] ?? '',
            description: $data['description'] ?? null,
            priority: isset($data['priority']) ? PriorityEnum::from($data['priority']) : null,
            type: TaskTypeEnum::from($data['type'] ?? 1),
            due_date: isset($data['due_date']) ? Carbon::parse($data['due_date']) : null,
            created_at: isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            updated_at: isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
            last_viewed_at: isset($data['last_viewed_at']) ? Carbon::parse($data['last_viewed_at']) : null,
            labels: $data['labels'] ?? [],
            subtasks: $data['subtasks'] ?? [],
            custom_data: $data['custom_data'] ?? [],
            position: $data['position'] ?? 0,
            comments_count: $data['comments_count'] ?? 0,
            tags: array_map(fn($tag) => TagDto::fromArray($tag), $data['tags'] ?? []),
            attachments: array_map(fn($att) => AttachmentDto::fromArray($att), $data['attachments'] ?? []),
            messages: array_map(fn($msg) => MessageDto::fromArray($msg), $data['messages'] ?? []),
            client: isset($data['client']) ? ClientDto::fromArray($data['client']) : null,
        );
    }

    public function isClient(): bool
    {
        return $this->type === TaskTypeEnum::CLIENT;
    }

    public function hasDueDate(): bool
    {
        return $this->due_date !== null;
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast();
    }
}
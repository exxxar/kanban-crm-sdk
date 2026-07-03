<?php

namespace Exxxar\Kanban\DTO;

use Carbon\Carbon;

class TaskCommentDto
{
    public function __construct(
        public int $id,
        public int $task_id,
        public string $author,
        public string $text,
        /** @var AttachmentDto[] */
        public array $attachments,
        public ?Carbon $created_at,
        public ?Carbon $updated_at,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? 0,
            task_id: $data['task_id'] ?? 0,
            author: $data['author'] ?? 'Аноним',
            text: $data['text'] ?? '',
            attachments: array_map(
                fn($a) => AttachmentDto::fromArray($a),
                $data['attachments'] ?? []
            ),
            created_at: isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            updated_at: isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
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
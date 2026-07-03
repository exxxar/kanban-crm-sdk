<?php

namespace Exxxar\Kanban\DTO;

use Carbon\Carbon;

class MessageDto
{
    public function __construct(
        public int $id,
        public int $task_id,
        public string $sender_type,
        public ?string $sender_label,
        public ?string $message,
        public array $payload,
        /** @var AttachmentDto[] */
        public array $attachments,
        public bool $is_read,
        public ?Carbon $created_at,
        public ?Carbon $updated_at,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? 0,
            task_id: $data['task_id'] ?? 0,
            sender_type: $data['sender_type'] ?? '',
            sender_label: $data['sender_label'] ?? null,
            message: $data['message'] ?? null,
            payload: $data['payload'] ?? [],
            attachments: array_map(
                fn($a) => AttachmentDto::fromArray($a),
                $data['attachments'] ?? []
            ),
            is_read: (bool) ($data['is_read'] ?? false),
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

    public function isFromClient(): bool
    {
        return $this->sender_type === 'external';
    }

    public function isFromManager(): bool
    {
        return $this->sender_type === 'manager';
    }
}
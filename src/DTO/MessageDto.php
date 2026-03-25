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
            id: $data['id'],
            task_id: $data['task_id'],

            sender_type: $data['sender_type'],
            sender_label: $data['sender_label'] ?? null,

            message: $data['message'] ?? null,
            payload: $data['payload'] ?? [],

            attachments: array_map(
                fn ($a) => AttachmentDto::fromArray($a),
                $data['attachments'] ?? []
            ),

            is_read: (bool)($data['is_read'] ?? false),

            created_at: isset($data['created_at'])
                ? Carbon::parse($data['created_at'])
                : null,

            updated_at: isset($data['updated_at'])
                ? Carbon::parse($data['updated_at'])
                : null,

        );
    }
}
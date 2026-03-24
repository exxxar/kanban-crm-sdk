<?php

namespace Exxxar\Kanban\DTO;

class AttachmentDto
{
    public function __construct(
        public string $name,
        public string $url,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            url: $data['url'],
        );
    }

    /**
     * @return AttachmentDto[]
     */
    public static function collection(array $items): array
    {
        return array_map(
            fn ($item) => self::fromArray($item),
            $items
        );
    }
}
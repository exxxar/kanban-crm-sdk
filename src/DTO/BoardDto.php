<?php

namespace Exxxar\Kanban\DTO;

class BoardDto
{
    public function __construct(
        public int $id,
        public string $uuid,
        public string $title,
        public ?string $description,
        public array $config,

        /** @var ColumnDto[] */
        public array $columns,

        /** @var TaskDto[] */
        public array $tasks,

        /** @var TagDto[] */
        public array $tags,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            uuid: $data['uuid'],
            title: $data['title'],
            description: $data['description'] ?? null,
            config: $data['config'] ?? [],

            columns: array_map(
                fn ($col) => ColumnDto::fromArray($col),
                $data['columns'] ?? []
            ),

            tasks: array_map(
                fn ($task) => TaskDto::fromArray($task),
                $data['tasks'] ?? []
            ),

            tags: array_map(
                fn ($tag) => TagDto::fromArray($tag),
                $data['tags'] ?? []
            ),
        );
    }

    /**
     * @return self[]
     */
    public static function collection(array $items): array
    {
        return array_map(
            fn ($item) => self::fromArray($item),
            $items
        );
    }
}
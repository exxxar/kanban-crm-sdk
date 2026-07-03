<?php

namespace Exxxar\Kanban\Services;

use Exxxar\Kanban\DTO\TagDto;

class Tags
{
    public function __construct(protected KanbanClient $client)
    {
    }

    /**
     * Получить все теги доски
     *
     * @return TagDto[]
     */
    public function list(string $boardUuid): array
    {
        $data = $this->client->request('GET', "boards/{$boardUuid}/tags");
        return TagDto::collection($data['tags'] ?? $data);
    }

    /**
     * Создать тег
     */
    public function create(string $boardUuid, string $name, string $color = '#999999'): TagDto
    {
        $data = $this->client->request('POST', "boards/{$boardUuid}/tags", [
            'json' => [
                'name' => $name,
                'color' => $color,
            ],
        ]);

        return TagDto::fromArray($data['tag'] ?? $data);
    }

    /**
     * Обновить тег
     */
    public function update(int $tagId, array $data): TagDto
    {
        $response = $this->client->request('PUT', "tags/{$tagId}", [
            'json' => $data,
        ]);

        return TagDto::fromArray($response['tag'] ?? $response);
    }

    /**
     * Удалить тег
     */
    public function delete(int $tagId): bool
    {
        $this->client->request('DELETE', "tags/{$tagId}");
        return true;
    }

    /**
     * Привязать теги к задаче
     */
    public function attachToTask(int $taskId, array $tagIds): bool
    {
        $this->client->request('POST', "tasks/{$taskId}/tags", [
            'json' => ['tag_ids' => $tagIds],
        ]);

        return true;
    }

    /**
     * Отвязать тег от задачи
     */
    public function detachFromTask(int $taskId, int $tagId): bool
    {
        $this->client->request('DELETE', "tasks/{$taskId}/tags/{$tagId}");
        return true;
    }
}
<?php

namespace Exxxar\Kanban\Services;

use Exxxar\Kanban\DTO\ColumnDto;

class Columns
{
    public function __construct(protected KanbanClient $client)
    {
    }

    /**
     * Создать колонку
     */
    public function create(string $boardUuid, string $title): ColumnDto
    {
        $data = $this->client->request('POST', "boards/{$boardUuid}/columns", [
            'json' => ['title' => $title],
        ]);

        return ColumnDto::fromArray($data['column'] ?? $data);
    }

    /**
     * Обновить колонку
     */
    public function update(int $columnId, array $data): ColumnDto
    {
        $response = $this->client->request('PUT', "columns/{$columnId}", [
            'json' => $data,
        ]);

        return ColumnDto::fromArray($response['column'] ?? $response);
    }

    /**
     * Переименовать колонку
     */
    public function rename(int $columnId, string $newTitle): ColumnDto
    {
        return $this->update($columnId, ['title' => $newTitle]);
    }

    /**
     * Удалить колонку
     */
    public function delete(int $columnId): bool
    {
        $this->client->request('DELETE', "columns/{$columnId}");
        return true;
    }

    /**
     * Изменить порядок колонок
     */
    public function reorder(string $boardUuid, array $columnIds): bool
    {
        $this->client->request('PUT', "boards/{$boardUuid}/columns/reorder", [
            'json' => ['order' => $columnIds],
        ]);

        return true;
    }

    /**
     * Обновить уведомления колонки
     */
    public function updateNotifications(int $columnId, array $settings): bool
    {
        $this->client->request('POST', "columns/{$columnId}/notifications", [
            'json' => ['notifications' => $settings],
        ]);

        return true;
    }
}
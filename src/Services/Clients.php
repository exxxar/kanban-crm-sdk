<?php

namespace Exxxar\Kanban\Services;

use Exxxar\Kanban\DTO\ClientDto;

class Clients
{
    public function __construct(protected KanbanClient $client)
    {
    }

    /**
     * Получить клиента задачи
     */
    public function get(int $taskId): ClientDto
    {
        $data = $this->client->request('GET', "tasks/{$taskId}/client");
        return ClientDto::fromArray($data['client'] ?? []);
    }

    /**
     * Обновить клиента
     */
    public function update(int $taskId, array $data): ClientDto
    {
        $response = $this->client->request('PUT', "tasks/{$taskId}/client", [
            'json' => $data,
        ]);

        return ClientDto::fromArray($response['client'] ?? []);
    }

    /**
     * Обновить кастомные данные клиента
     */
    public function updateCustomData(int $taskId, array $customData): ClientDto
    {
        return $this->update($taskId, ['custom_data' => $customData]);
    }

    /**
     * Обновить стоимость сделки
     */
    public function updateCost(int $taskId, float $cost): ClientDto
    {
        return $this->update($taskId, ['cost' => $cost]);
    }

    /**
     * Добавить ссылку клиенту
     */
    public function addLink(int $taskId, string $url, string $title): ClientDto
    {
        $client = $this->get($taskId);
        $links = $client->links;
        $links[] = ['url' => $url, 'title' => $title];

        return $this->update($taskId, ['links' => $links]);
    }
}
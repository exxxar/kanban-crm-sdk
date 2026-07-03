<?php

namespace Exxxar\Kanban\Services;

use Exxxar\Kanban\DTO\BoardDto;

class Boards
{
    public function __construct(protected KanbanClient $client)
    {
    }

    /**
     * Получить доску по UUID
     */
    public function get(string $uuid): BoardDto
    {
        $data = $this->client->request('GET', "boards/{$uuid}");
        return BoardDto::fromArray($data['board'] ?? []);
    }

    /**
     * Получить список досок
     *
     * @return BoardDto[]
     */
    public function list(): array
    {
        $data = $this->client->request('GET', 'boards');
        return BoardDto::collection($data['boards'] ?? []);
    }

    /**
     * Создать новую доску
     */
    public function create(string $title, ?string $description = null): BoardDto
    {
        $data = $this->client->request('POST', 'boards', [
            'json' => [
                'title' => $title,
                'description' => $description,
            ],
        ]);

        return BoardDto::fromArray($data['board'] ?? []);
    }

    /**
     * Обновить доску
     */
    public function update(string $uuid, array $data): BoardDto
    {
        $response = $this->client->request('PUT', "boards/{$uuid}", [
            'json' => $data,
        ]);

        return BoardDto::fromArray($response['board'] ?? []);
    }

    /**
     * Удалить доску
     */
    public function delete(string $uuid): bool
    {
        $this->client->request('DELETE', "boards/{$uuid}");
        return true;
    }

    /**
     * Применить шаблон к доске
     */
    public function applyTemplate(string $uuid, string $template): BoardDto
    {
        $data = $this->client->request('POST', "boards/{$uuid}/apply-template", [
            'json' => ['template' => $template],
        ]);

        return BoardDto::fromArray($data['board'] ?? []);
    }

    /**
     * Получить список доступных шаблонов
     */
    public function templates(): array
    {
        $data = $this->client->request('GET', 'boards/templates');
        return $data['templates'] ?? [];
    }

    /**
     * Обновить конфиг доски
     */
    public function updateConfig(string $uuid, array $config): array
    {
        $data = $this->client->request('POST', "boards/{$uuid}/config", [
            'json' => $config,
        ]);

        return $data['config'] ?? [];
    }
}
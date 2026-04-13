<?php

namespace Exxxar\Kanban\Services;

use Exxxar\Kanban\DTO\BoardDto;

class Boards
{
    public function __construct(protected KanbanClient $client)
    {
    }

    public function get(string $uuid): BoardDto
    {
        $data = $this->client->request('GET', "boards/{$uuid}");

        return BoardDto::fromArray($data["board"] ?? []);
    }

    /**
     * @return BoardDto[]
     */
    public function list(): array
    {
        $data = $this->client->request('GET', "boards");

        return BoardDto::collection($data["boards"] ?? []);
    }

    public function applyTemplate(string $uuid, string $template): BoardDto
    {
        $data = $this->client->request('POST', "boards/{$uuid}/apply-template", [
            'json' => ['template' => $template]
        ]);

        return BoardDto::fromArray($data["board"] ?? []);
    }
}
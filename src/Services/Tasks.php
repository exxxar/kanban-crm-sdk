<?php

namespace Exxxar\Kanban\Services;

use Exxxar\Kanban\DTO\AttachmentDto;
use Exxxar\Kanban\DTO\MessageDto;
use Exxxar\Kanban\DTO\TaskCommentDto;
use Exxxar\Kanban\DTO\TaskDto;

class Tasks
{
    public function __construct(protected KanbanClient $client)
    {
    }

    public function create(array $data): TaskDto
    {
        $response = $this->client->request('POST', 'task/create', [
            'json' => $data
        ]);

        return TaskDto::fromArray($response["task"] ?? []);
    }

    /**
     * @return TaskDto[]
     */
    public function getTasks(): array
    {
        $data = $this->client->request('GET', 'tasks');

        return array_map(
            fn ($item) => TaskDto::fromArray($item),
            $data
        );
    }

    public function getTask(int $taskId): TaskDto
    {
        $data = $this->client->request('GET', "task/{$taskId}");

        return TaskDto::fromArray($data["task"] ?? []);
    }

    public function comments(int $taskId): array
    {
        $data = $this->client->request('GET', "task/{$taskId}/comments");

        return TaskCommentDto::collection($data["comments"] ?? []);
    }

    public function addComment(int $taskId, array $data): TaskCommentDto
    {
        $response = $this->client->request('POST', "task/{$taskId}/comment", [
            'multipart' => $this->prepareMultipart($data)
        ]);

        return TaskCommentDto::fromArray($response["comment"] ?? []);
    }

    public function attachments(int $taskId): array
    {
        $data = $this->client->request('GET', "task/{$taskId}/attachments");

        return AttachmentDto::collection($data["attachments"] ?? []);
    }

    public function uploadAttachments(int $taskId, array $files): array
    {
        $response = $this->client->request('POST', "task/{$taskId}/attachments", [
            'multipart' => $this->prepareFiles($files)
        ]);

        return AttachmentDto::collection($response["attachments"] ?? []);
    }

    public function sendMessage(int $taskId, array $data): MessageDto
    {
        $response = $this->client->request('POST', "task/{$taskId}/message", [
            'multipart' => $this->prepareMultipart($data)
        ]);

        return MessageDto::fromArray($response["message"]);
    }

    // 🔧 helpers

    protected function prepareMultipart(array $data): array
    {
        $multipart = [];

        foreach ($data as $key => $value) {
            if ($key === 'files') continue;

            $multipart[] = [
                'name' => $key,
                'contents' => is_array($value) ? json_encode($value) : $value,
            ];
        }

        if (isset($data['files'])) {
            foreach ($data['files'] as $file) {
                $multipart[] = [
                    'name' => 'files[]',
                    'contents' => fopen($file, 'r'),
                    'filename' => basename($file),
                ];
            }
        }

        return $multipart;
    }

    protected function prepareFiles(array $files): array
    {
        $multipart = [];

        foreach ($files as $file) {
            $multipart[] = [
                'name' => 'files[]',
                'contents' => fopen($file, 'r'),
                'filename' => basename($file),
            ];
        }

        return $multipart;
    }
}
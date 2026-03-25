<?php

namespace Exxxar\Kanban\Services;

use Exxxar\Kanban\DTO\AttachmentDto;
use Exxxar\Kanban\DTO\MessageDto;
use Exxxar\Kanban\DTO\TaskCommentDto;
use Exxxar\Kanban\DTO\TaskDto;
use GuzzleHttp\Client;

class Tasks
{
    public function __construct(protected Client $http)
    {
    }

    public function create(array $data): TaskDto
    {
        return TaskDto::fromArray($this->http->post('task/create', ['json' => $data])->json());
    }

    /**
     * @return TaskDto[]
     */
    public function getTasks(): array
    {
        $data = $this->http->get('/tasks')->json();

        return array_map(
            fn($item) => TaskDto::fromArray($item),
            $data
        );
    }

    public function getTask(int $taskId): TaskDto
    {
        $content = $this->http->get("task/$taskId")->json();

        return TaskDto::fromArray($content);
    }

    public function comments($taskId): array
    {
        $data = $this->http->get("task/$taskId/comments")->json();

        return TaskCommentDto::collection($data);
    }

    public function addComment($taskId, array $data): TaskCommentDto
    {
        return TaskCommentDto::fromArray($this->http->post("task/$taskId/comment", ['multipart' => $data])->json());
    }

    public function attachments($taskId): array
    {
        $data = $this->http->get("task/$taskId/attachments")->json();
        return AttachmentDto::collection($data);
    }

    public function uploadAttachments($taskId, array $files): AttachmentDto
    {
        $data = $this->http->post("task/$taskId/attachments", ['multipart' => $files])->json();
        return AttachmentDto::fromArray($data);
    }

    public function sendMessage(int $taskId, array $data): MessageDto
    {
        $response = $this->http->post("task/$taskId/message", [
            'multipart' => $this->prepareMultipart($data)
        ])->json();

        return MessageDto::fromArray($response);
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

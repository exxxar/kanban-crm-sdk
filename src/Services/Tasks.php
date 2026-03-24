<?php

namespace Exxxar\Kanban\Services;

use Exxxar\Kanban\DTO\AttachmentDto;
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
}

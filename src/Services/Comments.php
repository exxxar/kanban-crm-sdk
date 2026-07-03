<?php

namespace Exxxar\Kanban\Services;

use Exxxar\Kanban\DTO\TaskCommentDto;

class Comments
{
    public function __construct(protected KanbanClient $client)
    {
    }

    /**
     * Получить комментарии задачи
     *
     * @return TaskCommentDto[]
     */
    public function list(int $taskId): array
    {
        $data = $this->client->request('GET', "tasks/{$taskId}/comments");
        return TaskCommentDto::collection($data['comments'] ?? []);
    }

    /**
     * Добавить комментарий
     */
    public function add(int $taskId, string $text, ?string $author = null, array $files = []): TaskCommentDto
    {
        $multipart = [
            ['name' => 'text', 'contents' => $text],
        ];

        if ($author) {
            $multipart[] = ['name' => 'author', 'contents' => $author];
        }

        foreach ($files as $file) {
            $multipart[] = [
                'name' => 'files[]',
                'contents' => fopen($file, 'r'),
                'filename' => basename($file),
            ];
        }

        $response = $this->client->request('POST', "tasks/{$taskId}/comments", [
            'multipart' => $multipart,
        ]);

        return TaskCommentDto::fromArray($response['comment'] ?? []);
    }

    /**
     * Обновить комментарий
     */
    public function update(int $commentId, string $text): TaskCommentDto
    {
        $response = $this->client->request('PUT', "comments/{$commentId}", [
            'json' => ['text' => $text],
        ]);

        return TaskCommentDto::fromArray($response['comment'] ?? []);
    }

    /**
     * Удалить комментарий
     */
    public function delete(int $commentId): bool
    {
        $this->client->request('DELETE', "comments/{$commentId}");
        return true;
    }
}
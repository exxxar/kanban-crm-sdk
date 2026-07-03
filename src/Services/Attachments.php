<?php

namespace Exxxar\Kanban\Services;

use Exxxar\Kanban\DTO\AttachmentDto;

class Attachments
{
    public function __construct(protected KanbanClient $client)
    {
    }

    /**
     * Получить вложения задачи
     *
     * @return AttachmentDto[]
     */
    public function list(int $taskId): array
    {
        $data = $this->client->request('GET', "tasks/{$taskId}/attachments");
        return AttachmentDto::collection($data['attachments'] ?? []);
    }

    /**
     * Загрузить вложения
     *
     * @param array $files Массив путей к файлам
     * @return AttachmentDto[]
     */
    public function upload(int $taskId, array $files): array
    {
        $multipart = [];

        foreach ($files as $file) {
            $multipart[] = [
                'name' => 'files[]',
                'contents' => fopen($file, 'r'),
                'filename' => basename($file),
            ];
        }

        $response = $this->client->request('POST', "tasks/{$taskId}/attachments", [
            'multipart' => $multipart,
        ]);

        return AttachmentDto::collection($response['attachments'] ?? []);
    }

    /**
     * Удалить вложение
     */
    public function delete(int $taskId, int $attachmentId): bool
    {
        $this->client->request('DELETE', "tasks/{$taskId}/attachments/{$attachmentId}");
        return true;
    }

    /**
     * Загрузить файл из строки (base64 или binary)
     */
    public function uploadFromString(int $taskId, string $content, string $filename): AttachmentDto
    {
        $multipart = [
            [
                'name' => 'files[]',
                'contents' => $content,
                'filename' => $filename,
            ],
        ];

        $response = $this->client->request('POST', "tasks/{$taskId}/attachments", [
            'multipart' => $multipart,
        ]);

        return AttachmentDto::fromArray($response['attachments'][0] ?? []);
    }
}
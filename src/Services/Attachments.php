<?php

namespace Exxxar\Kanban\Services;

use Exxxar\Kanban\DTO\AttachmentDto;

class Attachments
{
    public function __construct(protected KanbanClient $client)
    {
    }

    /**
     * @return AttachmentDto[]
     */
    public function list(int $taskId): array
    {
        $data = $this->client->request('GET', "task/{$taskId}/attachments");

        return AttachmentDto::collection($data["attachments"] ?? []);
    }

    /**
     * @return AttachmentDto[]
     */
    public function upload(int $taskId, array $files): array
    {
        $data = $this->client->request('POST', "task/{$taskId}/attachments", [
            'multipart' => $this->prepareFiles($files)
        ]);

        return AttachmentDto::collection($data["attachments"] ?? []);
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
<?php

namespace Exxxar\Kanban\Services;

use Exxxar\Kanban\DTO\TaskCommentDto;

class Comments
{
    public function __construct(protected KanbanClient $client)
    {
    }

    /**
     * @return TaskCommentDto[]
     */
    public function list(int $taskId): array
    {
        $data = $this->client->request('GET', "task/{$taskId}/comments");

        return TaskCommentDto::collection($data);
    }

    public function add(int $taskId, array $data): TaskCommentDto
    {
        $response = $this->client->request('POST', "task/{$taskId}/comment", [
            'multipart' => $this->prepareMultipart($data)
        ]);

        return TaskCommentDto::fromArray($response);
    }

    protected function prepareMultipart(array $data): array
    {
        $multipart = [];

        foreach ($data as $key => $value) {
            if ($key === 'files') continue;

            $multipart[] = [
                'name' => $key,
                'contents' => $value
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
}
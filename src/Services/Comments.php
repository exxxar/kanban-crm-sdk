<?php


namespace Exxxar\Kanban\Services;

use GuzzleHttp\Client;

use Exxxar\Kanban\DTO\TaskCommentDto;

class Comments
{
    public function __construct(protected Client $http)
    {
    }

    /**
     * @return TaskCommentDto[]
     */
    public function list(int $taskId): array
    {
        $data = $this->request('GET', "task/{$taskId}/comments");

        return TaskCommentDto::collection($data);
    }

    public function add(int $taskId, array $data): TaskCommentDto
    {
        $response = $this->request('POST', "task/{$taskId}/comment", [
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

    protected function request(string $method, string $uri, array $options = []): array
    {
        $response = $this->http->request($method, $uri, $options);

        return json_decode(
            $response->getBody()->getContents(),
            true
        );
    }
}

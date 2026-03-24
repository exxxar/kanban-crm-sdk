<?php


namespace Exxxar\Kanban\Services;

use GuzzleHttp\Client;
use Exxxar\Kanban\DTO\AttachmentDto;

class Attachments
{
    public function __construct(protected Client $http)
    {
    }

    /**
     * @return AttachmentDto[]
     */
    public function list(int $taskId): array
    {
        $data = $this->request('GET', "task/{$taskId}/attachments");

        return AttachmentDto::collection($data);
    }

    /**
     * @return AttachmentDto[]
     */
    public function upload(int $taskId, array $files): array
    {
        $data = $this->request('POST', "task/{$taskId}/attachments", [
            'multipart' => $this->prepareFiles($files)
        ]);

        return AttachmentDto::collection($data);
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

    protected function request(string $method, string $uri, array $options = []): array
    {
        $response = $this->http->request($method, $uri, $options);

        return json_decode(
            $response->getBody()->getContents(),
            true
        );
    }
}

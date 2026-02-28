<?php


namespace Exxxar\Kanban\Services;

use GuzzleHttp\Client;

class Attachments
{
    public function __construct(protected Client $http)
    {
    }

    public function list(int $taskId)
    {
        return $this->request('GET', "api/task/{$taskId}/attachments");
    }

    public function upload(int $taskId, array $files)
    {
        return $this->request('POST', "api/task/{$taskId}/attachments", [
            'multipart' => $this->prepareFiles($files)
        ]);
    }

    protected function prepareFiles(array $files): array
    {
        $multipart = [];

        foreach ($files as $file) {
            $multipart[] = [
                'name' => 'files[]',
                'contents' => fopen($file, 'r'),
                'filename' => basename($file)
            ];
        }

        return $multipart;
    }

    protected function request(string $method, string $uri, array $options = [])
    {
        return json_decode(
            $this->http->request($method, $uri, $options)->getBody()->getContents(),
            true
        );
    }
}

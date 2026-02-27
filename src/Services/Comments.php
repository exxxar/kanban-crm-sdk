<?php


namespace Exxxar\Kanban\Services;

use GuzzleHttp\Client;

class Comments
{
    public function __construct(protected Client $http)
    {
    }

    public function list(int $taskId)
    {
        return $this->request('GET', "task/{$taskId}/comments");
    }

    public function add(int $taskId, array $data)
    {
        return $this->request('POST', "task/{$taskId}/comment", [
            'multipart' => $this->prepareMultipart($data)
        ]);
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
                    'filename' => basename($file)
                ];
            }
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

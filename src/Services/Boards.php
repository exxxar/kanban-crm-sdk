<?php


namespace Exxxar\Kanban\Services;

use GuzzleHttp\Client;

class Boards
{
    public function __construct(protected Client $http)
    {
    }

    public function get(string $uuid)
    {
        return $this->request('GET', "api/boards/{$uuid}");
    }

    public function list()
    {
        return $this->request('GET', "api/boards");
    }

    public function applyTemplate(string $uuid, string $template)
    {
        return $this->request('POST', "api/boards/{$uuid}/apply-template", [
            'json' => ['template' => $template]
        ]);
    }

    protected function request(string $method, string $uri, array $options = [])
    {
        return json_decode(
            $this->http->request($method, $uri, $options)->getBody()->getContents(),
            true
        );
    }
}

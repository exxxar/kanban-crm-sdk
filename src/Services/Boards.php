<?php


namespace Exxxar\Kanban\Services;

use GuzzleHttp\Client;

use Exxxar\Kanban\DTO\BoardDto;

class Boards
{
    public function __construct(protected Client $http)
    {
    }

    public function get(string $uuid): BoardDto
    {
        $data = $this->request('GET', "boards/{$uuid}");

        return BoardDto::fromArray($data);
    }

    /**
     * @return BoardDto[]
     */
    public function list(): array
    {
        $data = $this->request('GET', "boards");

        return BoardDto::collection($data);
    }

    public function applyTemplate(string $uuid, string $template): BoardDto
    {
        $data = $this->request('POST', "boards/{$uuid}/apply-template", [
            'json' => ['template' => $template]
        ]);

        return BoardDto::fromArray($data);
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

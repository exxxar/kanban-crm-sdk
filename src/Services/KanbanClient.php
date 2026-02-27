<?php

namespace Exxxar\Kanban\Services;

use GuzzleHttp\Client;

class KanbanClient
{
    protected Client $http;

    public function __construct($baseUrl, $token = null)
    {
        $this->http = new Client([
            'base_uri' => $baseUrl,
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => $token ? "Bearer $token" : null,
            ]
        ]);
    }

    public function client(): static
    {
        return $this;
    }

    public function tasks()
    {
        return new Tasks($this->http);
    }

    public function comments()
    {
        return new Comments($this->http);
    }

    public function attachments()
    {
        return new Attachments($this->http);
    }

    public function boards()
    {
        return new Boards($this->http);
    }
}

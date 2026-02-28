<?php

namespace Exxxar\Kanban\Services;

use GuzzleHttp\Client;

class KanbanClient
{
    protected Client $http;

    protected string $token;
    protected string $baseUrl;

    public function __construct($baseUrl, $token = null)
    {
        $this->baseUrl = $baseUrl;
        $this->token = $token;
    }

    protected function prepareClient(){

        $this->http = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => $this->token ? "Bearer $this->token" : null,
            ]
        ]);
    }

    public function setToken($token){

        $this->token = $token;
        return $this;
    }

    public function client(): static
    {
        return $this;
    }

    public function tasks()
    {
        $this->prepareClient();
        return new Tasks($this->http);
    }

    public function comments()
    {
        $this->prepareClient();
        return new Comments($this->http);
    }

    public function attachments()
    {
        $this->prepareClient();
        return new Attachments($this->http);
    }

    public function boards()
    {
        $this->prepareClient();
        return new Boards($this->http);
    }
}

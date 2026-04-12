<?php

namespace Exxxar\Kanban\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class KanbanClient
{
    protected ?Client $http = null;

    protected string $baseUrl;
    protected ?string $token;

    public function __construct(string $baseUrl, ?string $token = null)
    {
        $this->baseUrl = $baseUrl;
        $this->token = $token;
    }

    protected function getClient(): Client
    {
        if ($this->http) {
            return $this->http;
        }

        $headers = [
            'Accept' => 'application/json',
        ];

        if ($this->token) {
            $headers['Authorization'] = "Bearer {$this->token}";
        }

        $this->http = new Client([
            'base_uri' => rtrim($this->baseUrl, '/') . '/',
            'headers' => $headers,
        ]);

        return $this->http;
    }

    // 🔥 единый helper для json
    public function request(string $method, string $uri, array $options = []): array
    {
        try {
            $response = $this->getClient()->request($method, $uri, $options);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function setToken(string $token): static
    {
        $this->token = $token;
        $this->http = null; // сброс клиента
        return $this;
    }

    public function client(): static
    {
        return $this;
    }

    // --- сервисы ---

    public function tasks(): Tasks
    {
        return new Tasks($this);
    }

    public function comments(): Comments
    {
        return new Comments($this);
    }

    public function attachments(): Attachments
    {
        return new Attachments($this);
    }

    public function boards(): Boards
    {
        return new Boards($this);
    }
}
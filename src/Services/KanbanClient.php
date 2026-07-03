<?php

namespace Exxxar\Kanban\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Exxxar\Kanban\Exceptions\KanbanException;
use Exxxar\Kanban\Exceptions\ValidationException;
use Exxxar\Kanban\Exceptions\NotFoundException;

class KanbanClient
{
    protected ?Client $http = null;
    protected string $baseUrl;
    protected ?string $token;
    protected array $options;

    public function __construct(string $baseUrl, ?string $token = null, array $options = [])
    {
        $this->baseUrl = rtrim($baseUrl, '/') . '/';
        $this->token = $token;
        $this->options = array_merge([
            'timeout' => 30,
            'connect_timeout' => 10,
            'retry' => ['times' => 3, 'sleep' => 100],
            'logging' => ['enabled' => true],
        ], $options);
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
            'base_uri' => $this->baseUrl,
            'headers' => $headers,
            'timeout' => $this->options['timeout'],
            'connect_timeout' => $this->options['connect_timeout'],
        ]);

        return $this->http;
    }

    /**
     * Выполняет запрос с retry логикой
     */
    public function request(string $method, string $uri, array $options = []): array
    {
        $method = strtoupper($method);
        $attempts = 0;
        $maxAttempts = $this->options['retry']['times'];
        $sleep = $this->options['retry']['sleep'];

        $this->log('info', "API Request: {$method} {$uri}", [
            'options' => $this->sanitizeOptions($options),
        ]);

        while ($attempts < $maxAttempts) {
            try {
                $response = $this->getClient()->request($method, $uri, $options);
                $body = $response->getBody()->getContents();
                $data = json_decode($body, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new KanbanException('Invalid JSON response: ' . json_last_error_msg());
                }

                $this->log('info', "API Response: {$method} {$uri}", [
                    'status' => $response->getStatusCode(),
                    'data' => $data,
                ]);

                return $data;

            } catch (RequestException $e) {
                $attempts++;

                $response = $e->getResponse();
                $statusCode = $response ? $response->getStatusCode() : 0;
                $body = $response ? json_decode($response->getBody()->getContents(), true) : null;

                $this->log('error', "API Error: {$method} {$uri}", [
                    'status' => $statusCode,
                    'error' => $e->getMessage(),
                    'attempt' => $attempts,
                ]);

                // Не повторяем клиентские ошибки (4xx кроме 429)
                if ($statusCode >= 400 && $statusCode < 500 && $statusCode !== 429) {
                    $this->handleClientError($statusCode, $body, $uri);
                }

                // Последний attempt — бросаем исключение
                if ($attempts >= $maxAttempts) {
                    throw new KanbanException(
                        "API request failed after {$maxAttempts} attempts: " . $e->getMessage(),
                        $statusCode,
                        $e
                    );
                }

                // Ждём перед retry
                usleep($sleep * 1000);
                $sleep *= 2; // exponential backoff
            } catch (GuzzleException $e) {
                throw new KanbanException('HTTP request failed: ' . $e->getMessage(), 0, $e);
            }
        }

        throw new KanbanException('Unexpected error in request loop');
    }

    /**
     * Обработка клиентских ошибок
     */
    protected function handleClientError(int $statusCode, ?array $body, string $uri): void
    {
        $message = $body['message'] ?? $body['error'] ?? 'Unknown error';

        match ($statusCode) {
            404 => throw new NotFoundException("Resource not found: {$uri}"),
            422 => throw new ValidationException($message, $body['errors'] ?? []),
            401, 403 => throw new KanbanException("Authentication failed: {$message}", $statusCode),
            default => throw new KanbanException("API error ({$statusCode}): {$message}", $statusCode),
        };
    }

    /**
     * Убирает чувствительные данные из логов
     */
    protected function sanitizeOptions(array $options): array
    {
        $sanitized = $options;

        if (isset($sanitized['multipart'])) {
            $sanitized['multipart'] = array_map(function ($item) {
                if (isset($item['contents']) && is_resource($item['contents'])) {
                    $item['contents'] = '[FILE RESOURCE]';
                }
                return $item;
            }, $sanitized['multipart']);
        }

        return $sanitized;
    }

    /**
     * Логирование запросов
     */
    protected function log(string $level, string $message, array $context = []): void
    {
        if (!$this->options['logging']['enabled']) {
            return;
        }

        try {
            Log::channel($this->options['logging']['channel'] ?? 'stack')
                ->{$level}("[KanbanSDK] {$message}", $context);
        } catch (\Throwable $e) {
            // Игнорируем ошибки логирования
        }
    }

    public function setToken(string $token): static
    {
        $this->token = $token;
        $this->http = null;
        return $this;
    }

    public function setBaseUrl(string $url): static
    {
        $this->baseUrl = rtrim($url, '/') . '/';
        $this->http = null;
        return $this;
    }

    // === Сервисы ===

    public function boards(): Boards
    {
        return new Boards($this);
    }

    public function columns(): Columns
    {
        return new Columns($this);
    }

    public function tasks(): Tasks
    {
        return new Tasks($this);
    }

    public function clients(): Clients
    {
        return new Clients($this);
    }

    public function tags(): Tags
    {
        return new Tags($this);
    }

    public function comments(): Comments
    {
        return new Comments($this);
    }

    public function attachments(): Attachments
    {
        return new Attachments($this);
    }

    public function messages(): Messages
    {
        return new Messages($this);
    }
}
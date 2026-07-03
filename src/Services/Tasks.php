<?php

namespace Exxxar\Kanban\Services;

use Exxxar\Kanban\DTO\TaskDto;
use Exxxar\Kanban\DTO\MessageDto;
use Exxxar\Kanban\DTO\ColumnDto;
use Exxxar\Kanban\Enums\TaskTypeEnum;
use Exxxar\Kanban\Enums\PriorityEnum;
use Exxxar\Kanban\Exceptions\ValidationException;
use Exxxar\Kanban\Exceptions\NotFoundException;

class Tasks
{
    public function __construct(protected KanbanClient $client)
    {
    }

    /**
     * Универсальный метод: отправляет сообщение существующей задаче/клиенту
     * или создаёт новую и сразу отправляет первое сообщение
     *
     * @param array $params Параметры:
     *   - task_id (int|null) - ID существующей задачи (если есть — создаёт не будет)
     *   - board_uuid (string) - UUID доски (обязательно при создании)
     *   - thread (int) - ПОРЯДКОВЫЙ НОМЕР КОЛОНКИ (0, 1, 2, 3...) по умолчанию 0
     *   - type (int) - тип: 1=задача, 2=клиент (по умолчанию 1)
     *   - title (string) - название (обязательно при создании)
     *   - description (string|null) - описание
     *   - priority (string) - приоритет: low/medium/high
     *   - labels (array) - метки
     *   - tag_ids (array) - ID тегов
     *   - due_date (string|null) - дата выполнения
     *   - client_data (array) - данные клиента (если type=2)
     *   - message (string) - текст сообщения (обязательно)
     *   - sender_type (string) - тип отправителя: 'external'/'manager'/'system'
     *   - sender_label (string|null) - метка отправителя
     *   - payload (array) - дополнительные данные
     *   - files (array) - пути к файлам
     *
     * @return array ['task_id' => int, 'message_id' => int, 'task' => TaskDto, 'message' => MessageDto, 'created' => bool]
     */
    public function sendMessageOrCreate(array $params): array
    {
        $this->validateSendParams($params);

        $taskId = $params['task_id'] ?? null;
        $messageText = $params['message'];
        $senderType = $params['sender_type'] ?? 'external';
        $senderLabel = $params['sender_label'] ?? null;
        $payload = $params['payload'] ?? [];
        $files = $params['files'] ?? [];

        // === ШАГ 1: Получаем или создаём задачу ===
        if ($taskId) {
            $task = $this->get($taskId);
        } else {
            $task = $this->createNewTask($params);
            $taskId = $task->id;
        }

        // === ШАГ 2: Отправляем сообщение ===
        $message = $this->sendMessage(
            taskId: $taskId,
            senderType: $senderType,
            message: $messageText,
            senderLabel: $senderLabel,
            payload: $payload,
            files: $files
        );

        return [
            'task_id' => $taskId,
            'message_id' => $message->id,
            'task' => $task,
            'message' => $message,
            'created' => !isset($params['task_id']),
        ];
    }

    /**
     * Валидация параметров
     */
    protected function validateSendParams(array $params): void
    {
        $errors = [];

        if (empty($params['task_id'])) {
            if (empty($params['board_uuid'])) {
                $errors['board_uuid'] = ['Board UUID is required when creating new task'];
            }
            if (empty($params['title'])) {
                $errors['title'] = ['Title is required when creating new task'];
            }
        }

        if (empty($params['message'])) {
            $errors['message'] = ['Message text is required'];
        }

        // Thread должен быть >= 0
        if (isset($params['thread']) && $params['thread'] < 0) {
            $errors['thread'] = ['Thread must be >= 0 (column position)'];
        }

        if (!empty($params['sender_type'])) {
            $validTypes = ['external', 'manager', 'system', 'bot', 'api'];
            if (!in_array($params['sender_type'], $validTypes)) {
                $errors['sender_type'] = ['Invalid sender type. Must be: ' . implode(', ', $validTypes)];
            }
        }

        if (($params['type'] ?? 1) === 2 && empty($params['client_data'])) {
            $errors['client_data'] = ['Client data is required when type=2'];
        }

        if (!empty($errors)) {
            throw new ValidationException('Validation failed', $errors);
        }
    }

    /**
     * Создание новой задачи
     */
    protected function createNewTask(array $params): TaskDto
    {
        $type = $params['type'] ?? 1;
        $boardUuid = $params['board_uuid'];
        $thread = $params['thread'] ?? 0; // ПОРЯДКОВЫЙ НОМЕР КОЛОНКИ
        $title = $params['title'];
        $description = $params['description'] ?? null;
        $priority = $params['priority'] ?? 'low';
        $labels = $params['labels'] ?? [];
        $tagIds = $params['tag_ids'] ?? [];
        $dueDate = $params['due_date'] ?? null;

        if ($type === 2) {
            return $this->createClient(
                boardUuid: $boardUuid,
                thread: $thread,
                title: $title,
                clientData: $params['client_data'],
                description: $description,
                priority: PriorityEnum::from($priority),
                labels: $labels ?: ['client'],
                tag_ids: $tagIds,
                due_date: $dueDate
            );
        } else {
            return $this->createTask(
                boardUuid: $boardUuid,
                thread: $thread,
                title: $title,
                description: $description,
                priority: PriorityEnum::from($priority),
                labels: $labels,
                tag_ids: $tagIds,
                due_date: $dueDate
            );
        }
    }

    /**
     * Отправка сообщения
     */
    public function sendMessage(
        int $taskId,
        string $senderType,
        ?string $message = null,
        ?string $senderLabel = null,
        array $payload = [],
        array $files = []
    ): MessageDto {
        $multipart = [
            ['name' => 'sender_type', 'contents' => $senderType],
        ];

        if ($message !== null) {
            $multipart[] = ['name' => 'message', 'contents' => $message];
        }

        if ($senderLabel !== null) {
            $multipart[] = ['name' => 'sender_label', 'contents' => $senderLabel];
        }

        if (!empty($payload)) {
            $multipart[] = ['name' => 'payload', 'contents' => json_encode($payload)];
        }

        foreach ($files as $file) {
            $multipart[] = [
                'name' => 'files[]',
                'contents' => fopen($file, 'r'),
                'filename' => basename($file),
            ];
        }

        $response = $this->client->request('POST', "tasks/{$taskId}/messages", [
            'multipart' => $multipart,
        ]);

        return MessageDto::fromArray($response['message'] ?? []);
    }

    /**
     * Продолжить диалог
     */
    public function continueDialog(
        int $taskId,
        string $message,
        string $senderType = 'external',
        ?string $senderLabel = null,
        array $payload = [],
        array $files = []
    ): array {
        $messageDto = $this->sendMessage(
            taskId: $taskId,
            senderType: $senderType,
            message: $message,
            senderLabel: $senderLabel,
            payload: $payload,
            files: $files
        );

        return [
            'task_id' => $taskId,
            'message_id' => $messageDto->id,
            'message' => $messageDto,
            'created' => false,
        ];
    }

    /**
     * Создать задачу
     */
    public function create(string $boardUuid, array $data): TaskDto
    {
        $response = $this->client->request('POST', "boards/{$boardUuid}/tasks", [
            'json' => $data,
        ]);

        return TaskDto::fromArray($response['task'] ?? []);
    }

    /**
     * Создать обычную задачу (type=1)
     *
     * @param int $thread ПОРЯДКОВЫЙ НОМЕР КОЛОНКИ (0, 1, 2, 3...)
     */
    public function createTask(
        string $boardUuid,
        int $thread,
        string $title,
        ?string $description = null,
        PriorityEnum $priority = PriorityEnum::LOW,
        array $labels = [],
        array $tag_ids = [],
        ?string $due_date = null,
        array $subtasks = [],
        array $custom_data = []
    ): TaskDto {
        return $this->create($boardUuid, [
            'thread' => $thread,
            'title' => $title,
            'description' => $description,
            'priority' => $priority->value,
            'type' => TaskTypeEnum::TASK->value,
            'labels' => $labels,
            'tag_ids' => $tag_ids,
            'due_date' => $due_date,
            'subtasks' => $subtasks,
            'custom_data' => $custom_data,
        ]);
    }

    /**
     * Создать клиента (type=2)
     *
     * @param int $thread ПОРЯДКОВЫЙ НОМЕР КОЛОНКИ (0, 1, 2, 3...)
     */
    public function createClient(
        string $boardUuid,
        int $thread,
        string $title,
        array $clientData,
        ?string $description = null,
        PriorityEnum $priority = PriorityEnum::MEDIUM,
        array $labels = ['client'],
        array $tag_ids = [],
        ?string $due_date = null
    ): TaskDto {
        return $this->create($boardUuid, [
            'thread' => $thread,
            'title' => $title,
            'description' => $description,
            'priority' => $priority->value,
            'type' => TaskTypeEnum::CLIENT->value,
            'labels' => $labels,
            'tag_ids' => $tag_ids,
            'due_date' => $due_date,
            'client' => $clientData,
        ]);
    }

    /**
     * Получить задачу по ID
     */
    public function get(int $taskId): TaskDto
    {
        $data = $this->client->request('GET', "tasks/{$taskId}");
        return TaskDto::fromArray($data['task'] ?? []);
    }

    /**
     * Получить все задачи доски
     */
    public function getAll(string $boardUuid): array
    {
        $data = $this->client->request('GET', "boards/{$boardUuid}/tasks");

        return array_map(
            fn($item) => TaskDto::fromArray($item),
            $data['tasks'] ?? []
        );
    }

    /**
     * Обновить задачу
     */
    public function update(int $taskId, array $data): TaskDto
    {
        $response = $this->client->request('PUT', "tasks/{$taskId}", [
            'json' => $data,
        ]);

        return TaskDto::fromArray($response['task'] ?? []);
    }

    /**
     * Удалить задачу
     */
    public function delete(int $taskId): bool
    {
        $this->client->request('DELETE', "tasks/{$taskId}");
        return true;
    }

    /**
     * Дублировать задачу
     */
    public function duplicate(int $taskId): TaskDto
    {
        $response = $this->client->request('POST', "tasks/{$taskId}/duplicate");
        return TaskDto::fromArray($response['task'] ?? []);
    }

    /**
     * Переместить задачу в другую колонку
     */
    public function move(int $taskId, int $columnId): bool
    {
        $this->client->request('POST', 'tasks/move', [
            'json' => [
                'task_id' => $taskId,
                'column_id' => $columnId,
            ],
        ]);

        return true;
    }

    /**
     * Изменить порядок задач в колонке
     */
    public function reorder(int $columnId, array $taskIds): bool
    {
        $this->client->request('PUT', "columns/{$columnId}/tasks/reorder", [
            'json' => ['order' => $taskIds],
        ]);

        return true;
    }

    /**
     * Отметить задачу как просмотренную
     */
    public function markViewed(int $taskId): bool
    {
        $this->client->request('POST', "tasks/{$taskId}/view");
        return true;
    }
}
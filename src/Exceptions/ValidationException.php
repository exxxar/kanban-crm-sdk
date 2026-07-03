<?php

namespace Exxxar\Kanban\Exceptions;

class ValidationException extends KanbanException
{
    protected array $errors;

    public function __construct(string $message, array $errors = [])
    {
        parent::__construct($message, 422);
        $this->errors = $errors;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    public function getError(string $field): ?array
    {
        return $this->errors[$field] ?? null;
    }
}
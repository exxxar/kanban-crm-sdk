<?php

namespace Exxxar\Kanban\Exceptions;

class NotFoundException extends KanbanException
{
    public function __construct(string $message = 'Resource not found')
    {
        parent::__construct($message, 404);
    }
}
<?php

namespace Exxxar\Kanban\Enums;

use Exxxar\Kanban\Services\KanbanClient;


enum KanbanTaskTypeEnum: int
{
    case USER = 1;
    case ORDER = 2;
    case TEXT = 3;
    case FINANCE = 4;
    case DEVELOPMENT = 5;

    public function label(): string
    {
        return match($this) {
            self::USER => 'user',
            self::ORDER => 'order',
            self::TEXT => 'text',
            self::FINANCE => 'finance',
            self::DEVELOPMENT => 'development',
        };
    }

}

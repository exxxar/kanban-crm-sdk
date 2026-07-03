<?php

namespace Exxxar\Kanban\Enums;

enum PriorityEnum: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';

    public function label(): string
    {
        return match($this) {
            self::LOW => 'Низкий',
            self::MEDIUM => 'Средний',
            self::HIGH => 'Высокий',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::LOW => '⬇️',
            self::MEDIUM => '➖',
            self::HIGH => '⬆️',
        };
    }
}
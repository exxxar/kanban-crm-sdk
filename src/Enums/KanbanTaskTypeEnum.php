<?php

namespace Exxxar\Kanban\Enums;

enum TaskTypeEnum: int
{
    case TASK = 1;        // Обычная задача
    case CLIENT = 2;      // Клиент
    case TEXT = 3;        // Текст
    case FINANCE = 4;     // Финансы
    case DEVELOPMENT = 5; // Разработка
    case ORDER = 6;       // Заказ

    public function label(): string
    {
        return match($this) {
            self::TASK => 'Задача',
            self::CLIENT => 'Клиент',
            self::TEXT => 'Текст',
            self::FINANCE => 'Финансы',
            self::DEVELOPMENT => 'Разработка',
            self::ORDER => 'Заказ',
        };
    }

    public function isClient(): bool
    {
        return $this === self::CLIENT;
    }
}
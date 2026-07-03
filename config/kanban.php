<?php

return [
    'base_url' => env('KANBAN_API_URL', 'https://kanban.example.com/api'),
    'token' => env('KANBAN_API_TOKEN'),

    // Таймауты (в секундах)
    'timeout' => env('KANBAN_TIMEOUT', 30),
    'connect_timeout' => env('KANBAN_CONNECT_TIMEOUT', 10),

    // Retry логика
    'retry' => [
        'times' => env('KANBAN_RETRY_TIMES', 3),
        'sleep' => env('KANBAN_RETRY_SLEEP', 100), // миллисекунды
    ],

    // Логирование
    'logging' => [
        'enabled' => env('KANBAN_LOG_ENABLED', true),
        'channel' => env('KANBAN_LOG_CHANNEL', 'stack'),
    ],
];
# Kanban Laravel SDK

Laravel‑пакет для интеграции с Kanban API. Предоставляет фасад `Kanban`, сервис‑клиент и классы для работы с задачами, досками, комментариями и вложениями. Подходит для микросервисов, внешних интеграций, Telegram‑ботов, мобильных приложений и любых Laravel‑проектов.

---

## Установка

```bash
composer require exxxar/kanban-laravel
```

---

# Публикация конфига
```bash
php artisan vendor:publish --tag=config
```
Будет создан файл:
`config/kanban.php`

# Настройка

Добавьте параметры в .env:
```env
KANBAN_API_URL=https://example.com/api
KANBAN_API_TOKEN=your_api_token_here
```
# Использование
### Фасад:
```php
use Kanban;
```
# Работа с задачами
Создание задачи
```php
$response = Kanban::tasks()->create([
    'thread' => 1,
    'type' => 'order',
    'payload' => [
        'title' => 'Новая задача',
        'description' => 'Описание задачи'
    ]
]);
```
Получение комментариев задачи
```php
$comments = Kanban::tasks()->comments(55);
```
Добавление комментария
```php
Kanban::tasks()->addComment(55, [
    'author' => 'Алексей',
    'text' => 'Готово!',
]);
```
Загрузка вложений
```php
Kanban::tasks()->uploadAttachments(55, [
    storage_path('app/test.jpg'),
    storage_path('app/manual.pdf'),
]);
```
# Работа с досками
Получить доску
```php
$board = Kanban::boards()->get('123e4567-e89b-12d3-a456-426614174000');
```

Получить список досок
```php
$list = Kanban::boards()->list();
```

Применить шаблон к доске
```php
Kanban::boards()->applyTemplate(
    '123e4567-e89b-12d3-a456-426614174000',
    'classic'
);
```
# Работа с комментариями
Получить список комментариев
```php
$comments = Kanban::comments()->list(55);
```
Добавить комментарий
```php
Kanban::comments()->add(55, [
    'author' => 'Алексей',
    'text' => 'Комментарий через SDK',
]);
```
# Работа с вложениями
Получить вложения
```php
$attachments = Kanban::attachments()->list(55);
```
Загрузить файлы
```php
Kanban::attachments()->upload(55, [
    '/path/to/file1.jpg',
    '/path/to/file2.pdf',
]);
```
Использование через DI
```php
use Alexey\Kanban\Services\KanbanClient;

public function handle(KanbanClient $kanban)
{
    $task = $kanban->tasks()->create([...]);
}
```
# Общий пример

```php
use Kanban;

// Создать задачу
Kanban::tasks()->create([
    'thread' => 1,
    'type' => 'order',
    'payload' => ['title' => 'Новая задача']
]);

// Получить доску
$board = Kanban::boards()->get('123');

// Добавить комментарий
Kanban::comments()->add(55, [
    'author' => 'Алексей',
    'text' => 'Готово!',
]);

// Загрузить вложения
Kanban::attachments()->upload(55, [
    '/path/to/file1.jpg',
    '/path/to/file2.pdf',
]);
```

# Структура пакета

```
kanban-laravel/
 ├── src/
 │    ├── KanbanServiceProvider.php
 │    ├── Facades/
 │    │     └── Kanban.php
 │    ├── Services/
 │    │     ├── KanbanClient.php
 │    │     ├── Boards.php
 │    │     ├── Tasks.php
 │    │     ├── Comments.php
 │    │     └── Attachments.php
 ├── config/
 │    └── kanban.php
 ├── composer.json
 ├── README.md
 └── tests/
 ```

# Лицензия
MIT License.

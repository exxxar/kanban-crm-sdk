<?php

namespace Exxxar\Kanban\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Exxxar\Kanban\Services\Boards boards()
 * @method static \Exxxar\Kanban\Services\Columns columns()
 * @method static \Exxxar\Kanban\Services\Tasks tasks()
 * @method static \Exxxar\Kanban\Services\Clients clients()
 * @method static \Exxxar\Kanban\Services\Tags tags()
 * @method static \Exxxar\Kanban\Services\Comments comments()
 * @method static \Exxxar\Kanban\Services\Attachments attachments()
 * @method static \Exxxar\Kanban\Services\Messages messages()
 * @method static static setToken(string $token)
 *
 * @see \Exxxar\Kanban\Services\KanbanClient
 */
class Kanban extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Exxxar\Kanban\Services\KanbanClient::class;
    }
}
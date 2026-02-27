<?php

namespace Exxxar\Kanban\Facades;

use Alexey\Kanban\Services\KanbanClient;
use Illuminate\Support\Facades\Facade;

/**
 * @method static KanbanClient client()
 * @method static \Exxxar\Kanban\Services\Boards boards()
 * @method static \Exxxar\Kanban\Services\Tasks tasks()
 * @method static \Exxxar\Kanban\Services\Comments comments()
 * @method static \Exxxar\Kanban\Services\Attachments attachments()

 */
class Kanban extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Alexey\Kanban\Services\KanbanClient::class;
    }


}

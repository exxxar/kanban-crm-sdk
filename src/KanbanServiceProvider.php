<?php

namespace Exxxar\Kanban;

use Illuminate\Support\ServiceProvider;
use Exxxar\Kanban\Services\KanbanClient;

class KanbanServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/kanban.php', 'kanban');

        $this->app->singleton(KanbanClient::class, function ($app) {
            return new KanbanClient(
                config('kanban.base_url'),
                config('kanban.token'),
                [
                    'timeout' => config('kanban.timeout', 30),
                    'connect_timeout' => config('kanban.connect_timeout', 10),
                    'retry' => config('kanban.retry', ['times' => 3, 'sleep' => 100]),
                    'logging' => config('kanban.logging', ['enabled' => true]),
                ]
            );
        });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/kanban.php' => config_path('kanban.php'),
            ], 'kanban-config');
        }
    }
}
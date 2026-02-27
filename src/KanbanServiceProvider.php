<?php

namespace Exxxar\Kanban;

use Illuminate\Support\ServiceProvider;
use Exxxar\Kanban\Services\KanbanClient;

class KanbanServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/kanban.php', 'kanban');

        $this->app->singleton(KanbanClient::class, function ($app) {
            return new KanbanClient(
                config('kanban.base_url'),
                config('kanban.token')
            );
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/kanban.php' => config_path('kanban.php'),
        ], 'config');
    }
}

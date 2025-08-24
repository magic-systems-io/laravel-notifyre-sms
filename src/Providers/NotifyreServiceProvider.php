<?php

namespace MagicSystemsIO\Notifyre\Providers;

use Illuminate\Support\ServiceProvider;
use MagicSystemsIO\Notifyre\Contracts\NotifyreManager;
use MagicSystemsIO\Notifyre\Services\NotifyreService;

class NotifyreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->register(ConfigurationServiceProvider::class);
        $this->app->register(ServicesServiceProvider::class);
        $this->app->register(MigrationServiceProvider::class);
        $this->app->register(CommandServiceProvider::class);

        $this->app->bind(NotifyreManager::class, function ($app) {
            return $app->make(NotifyreService::class);
        });
    }
}

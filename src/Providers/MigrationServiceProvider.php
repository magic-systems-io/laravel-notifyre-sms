<?php

namespace MagicSystemsIO\Notifyre\Providers;

use Illuminate\Support\ServiceProvider;

class MigrationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (!config('notifyre.database.enabled')) {
            return;
        }

        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }
}

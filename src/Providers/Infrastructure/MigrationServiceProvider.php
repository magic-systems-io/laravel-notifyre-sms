<?php

namespace Arbi\Notifyre\Providers\Infrastructure;

use Illuminate\Support\ServiceProvider;

class MigrationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../../database/migrations/create_notifyre_tables.php');
    }
}

<?php

namespace MagicSystemsIO\Notifyre\Providers;

use Illuminate\Support\ServiceProvider;

class NotifyreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->register(ConfigurationServiceProvider::class);
        $this->app->register(ServicesServiceProvider::class);
        $this->app->register(MigrationServiceProvider::class);
        $this->app->register(CommandServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(NotifyreLoggingServiceProvider::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/notifyre.php' => config_path('notifyre.php'),
            ], 'notifyre-config');

            $this->publishes([
                __DIR__ . '/../../database/migrations/create_notifyre_tables.php' => database_path('migrations/' . date('Y_m_d_His') . '_create_notifyre_tables.php'),
            ], 'notifyre-migrations');

            $this->publishes([
                __DIR__ . '/../../config/notifyre.php' => config_path('notifyre.php'),
                __DIR__ . '/../database/migrations/create_notifyre_tables.php' => database_path('migrations/' . date('Y_m_d_His') . '_create_notifyre_tables.php'),
            ], 'notifyre');
        }
    }
}

<?php

namespace MagicSystemsIO\Notifyre\Providers\Infrastructure;

use Illuminate\Support\ServiceProvider;

class ConfigurationServiceProvider extends ServiceProvider
{
    private const string CONFIG_PATH = __DIR__ . '/../../../config/notifyre.php';

    public const string CONFIG_KEY = 'notifyre';

    public function register(): void
    {
        $this->mergeConfigFrom(self::CONFIG_PATH, self::CONFIG_KEY);
    }

    public function boot(): void
    {
        if (method_exists($this, 'publishes') && function_exists('config_path')) {
            $this->publishes([
                self::CONFIG_PATH => config_path('notifyre.php'),
            ], 'notifyre-config');
        }
    }
}

<?php

namespace MagicSystemsIO\Notifyre\Providers;

use Illuminate\Support\ServiceProvider;

class ConfigurationServiceProvider extends ServiceProvider
{
    public const string CONFIG_PATH = __DIR__ . '/../../config/notifyre.php';

    public const string CONFIG_KEY = 'notifyre';

    public function register(): void
    {
        $this->mergeConfigFrom(self::CONFIG_PATH, self::CONFIG_KEY);
    }
}

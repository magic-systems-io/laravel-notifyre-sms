<?php

namespace MagicSystemsIO\Notifyre\Providers;

use Illuminate\Support\ServiceProvider;
use MagicSystemsIO\Notifyre\Services\NotifyreLogger;

class NotifyreLoggingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(NotifyreLogger::class, function () {
            return new NotifyreLogger();
        });
    }
}

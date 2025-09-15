<?php

namespace MagicSystemsIO\Notifyre\Providers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;
use MagicSystemsIO\Notifyre\Services\NotifyreLogger;

class NotifyreLoggingServiceProvider extends ServiceProvider
{
    /**
     * @throws BindingResolutionException
     */
    public function register(): void
    {
        $this->app->singleton(NotifyreLogger::class, function () {
            return new NotifyreLogger();
        });
    }

    public function boot(): void
    {
        $this->app->make('log')->extend('notifyre', function ($app, $config) {
            return (new NotifyreLogger())($config);
        });
    }
}

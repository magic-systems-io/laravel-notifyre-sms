<?php

namespace MagicSystemsIO\Notifyre\Providers;

use Illuminate\Contracts\Container\BindingResolutionException;
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

    /**
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        $this->app->make('log')->extend('notifyre', function ($app, $config) {
            return (new NotifyreLogger())($config);
        });

        if (!config('logging.channels.notifyre')) {
            config([
                'logging.channels.notifyre' => [
                    'driver' => 'notifyre',
                ],
            ]);
        }
    }
}

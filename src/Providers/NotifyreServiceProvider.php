<?php

namespace Arbi\Notifyre\Providers;

use Arbi\Notifyre\Channels\NotifyreChannel;
use Arbi\Notifyre\Commands\NotifyreSmsSendCommand;
use Arbi\Notifyre\Commands\PublishNotifyreAllCommand;
use Arbi\Notifyre\Commands\PublishNotifyreConfigCommand;
use Arbi\Notifyre\Commands\PublishNotifyreEnvCommand;
use Arbi\Notifyre\Services\DriverFactory;
use Arbi\Notifyre\Services\NotifyreService;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class NotifyreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/notifyre.php', 'notifyre');

        $this->app->singleton('notifyre', function ($app) {
            return new NotifyreService(new DriverFactory());
        });

        $this->app->singleton(DriverFactory::class, function () {
            return new DriverFactory();
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/notifyre.php' => config_path('notifyre.php'),
        ], 'notifyre-config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                NotifyreSmsSendCommand::class,
                PublishNotifyreConfigCommand::class,
                PublishNotifyreEnvCommand::class,
                PublishNotifyreAllCommand::class,
            ]);
        }

        Notification::resolved(function (ChannelManager $service) {
            $service->extend('notifyre', function ($app) {
                return new NotifyreChannel(new DriverFactory());
            });
        });
    }
}

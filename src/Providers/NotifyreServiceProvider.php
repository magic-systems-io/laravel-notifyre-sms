<?php

namespace Arbi\Notifyre\Providers;

use Arbi\Notifyre\Channels\NotifyreChannel;
use Arbi\Notifyre\Commands\NotifyreSmsSendCommand;
use Arbi\Notifyre\Commands\PublishNotifyreAllCommand;
use Arbi\Notifyre\Commands\PublishNotifyreConfigCommand;
use Arbi\Notifyre\Commands\PublishNotifyreEnvCommand;
use Arbi\Notifyre\Contracts\NotifyreDriverFactoryInterface;
use Arbi\Notifyre\Contracts\NotifyreServiceInterface;
use Arbi\Notifyre\Services\DriverFactory;
use Arbi\Notifyre\Services\NotifyreService;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class NotifyreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (method_exists($this, 'mergeConfigFrom') && function_exists('config_path')) {
            $this->mergeConfigFrom(__DIR__ . '/../../config/notifyre.php', 'notifyre');
        }

        $this->app->singleton('notifyre', function ($app) {
            return new NotifyreService($app->make(NotifyreDriverFactoryInterface::class));
        });

        $this->app->singleton(NotifyreServiceInterface::class, function ($app) {
            return $app->make('notifyre');
        });

        $this->app->singleton(DriverFactory::class, function () {
            return new DriverFactory();
        });

        $this->app->singleton(NotifyreDriverFactoryInterface::class, function () {
            return new DriverFactory();
        });
    }

    public function boot(): void
    {
        if (method_exists($this, 'publishes') && function_exists('config_path')) {
            $this->publishes([
                __DIR__ . '/../../config/notifyre.php' => config_path('notifyre.php'),
            ], 'notifyre-config');
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                NotifyreSmsSendCommand::class,
                PublishNotifyreConfigCommand::class,
                PublishNotifyreEnvCommand::class,
                PublishNotifyreAllCommand::class,
            ]);
        }

        if (class_exists(Notification::class) && method_exists(Notification::class, 'resolved')) {
            Notification::resolved(function (ChannelManager $service) {
                $service->extend('notifyre', function ($app) {
                    return new NotifyreChannel($app->make(NotifyreDriverFactoryInterface::class));
                });
            });
        }
    }
}

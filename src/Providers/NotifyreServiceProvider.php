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
    private const string CONFIG_PATH = __DIR__ . '/../../config/notifyre.php';

    private const array COMMANDS = [
        NotifyreSmsSendCommand::class,
        PublishNotifyreConfigCommand::class,
        PublishNotifyreEnvCommand::class,
        PublishNotifyreAllCommand::class,
    ];

    private const array SINGLETONS = [
        'notifyre' => NotifyreService::class,
        NotifyreServiceInterface::class => 'notifyre',
        DriverFactory::class => DriverFactory::class,
        NotifyreDriverFactoryInterface::class => DriverFactory::class,
    ];

    public function register(): void
    {
        if (method_exists($this, 'mergeConfigFrom') && function_exists('config_path')) {
            $this->mergeConfigFrom(self::CONFIG_PATH, 'notifyre');
        }

        foreach (self::SINGLETONS as $abstract => $concrete) {
            $this->app->singleton($abstract, function ($app) use ($concrete) {
                return match ($concrete) {
                    NotifyreService::class => new NotifyreService($app->make(NotifyreDriverFactoryInterface::class)),
                    'notifyre' => $app->make('notifyre'),
                    DriverFactory::class => new DriverFactory(),
                };
            });
        }
    }

    public function boot(): void
    {
        $this->publishConfig();
        $this->registerCommands();
        $this->extendNotificationChannel();
    }

    private function publishConfig(): void
    {
        if (method_exists($this, 'publishes') && function_exists('config_path')) {
            $this->publishes([
                self::CONFIG_PATH => config_path('notifyre.php'),
            ], 'notifyre-config');
        }
    }

    private function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands(self::COMMANDS);
        }
    }

    private function extendNotificationChannel(): void
    {
        if (class_exists(Notification::class) && method_exists(Notification::class, 'resolved')) {
            Notification::resolved(function (ChannelManager $service) {
                $service->extend('notifyre', function ($app) {
                    return new NotifyreChannel($app->make(NotifyreDriverFactoryInterface::class));
                });
            });
        }
    }
}

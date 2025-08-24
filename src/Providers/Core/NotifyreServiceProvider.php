<?php

namespace MagicSystemsIO\Notifyre\Providers\Core;

use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;
use MagicSystemsIO\Notifyre\Channels\NotifyreChannel;
use MagicSystemsIO\Notifyre\Commands\NotifyreSmsSendCommand;
use MagicSystemsIO\Notifyre\Commands\PublishNotifyreAllCommand;
use MagicSystemsIO\Notifyre\Commands\PublishNotifyreConfigCommand;
use MagicSystemsIO\Notifyre\Commands\PublishNotifyreEnvCommand;
use MagicSystemsIO\Notifyre\Contracts\NotifyreDriverFactoryInterface;
use MagicSystemsIO\Notifyre\Contracts\NotifyreServiceInterface;
use MagicSystemsIO\Notifyre\Providers\Features\CommandServiceProvider;
use MagicSystemsIO\Notifyre\Providers\Features\ModelServiceProvider;
use MagicSystemsIO\Notifyre\Providers\Features\RouteServiceProvider;
use MagicSystemsIO\Notifyre\Providers\Infrastructure\ConfigurationServiceProvider;
use MagicSystemsIO\Notifyre\Providers\Infrastructure\ContractServiceProvider;
use MagicSystemsIO\Notifyre\Providers\Infrastructure\FacadeServiceProvider;
use MagicSystemsIO\Notifyre\Providers\Infrastructure\MigrationServiceProvider;
use MagicSystemsIO\Notifyre\Services\DriverFactory;
use MagicSystemsIO\Notifyre\Services\NotifyreService;

class NotifyreServiceProvider extends ServiceProvider
{
    private const string CONFIG_PATH = __DIR__ . '/../../../config/notifyre.php';

    private const array COMMANDS = [
        NotifyreSmsSendCommand::class,
        PublishNotifyreConfigCommand::class,
        PublishNotifyreEnvCommand::class,
        PublishNotifyreAllCommand::class,
    ];

    private const array PROVIDERS = [
        ConfigurationServiceProvider::class,
        MigrationServiceProvider::class,
        CommandServiceProvider::class,
        RouteServiceProvider::class,
        ModelServiceProvider::class,
        ContractServiceProvider::class,
        FacadeServiceProvider::class,
    ];

    public function register(): void
    {
        // Register all feature providers
        foreach (self::PROVIDERS as $provider) {
            $this->app->register($provider);
        }

        // Merge configuration
        if (method_exists($this, 'mergeConfigFrom') && function_exists('config_path')) {
            $this->mergeConfigFrom(self::CONFIG_PATH, 'notifyre');
        }

        // Register core services
        $this->app->singleton(NotifyreDriverFactoryInterface::class, DriverFactory::class);
        $this->app->singleton(DriverFactory::class, DriverFactory::class);

        $this->app->singleton(NotifyreServiceInterface::class, function ($app) {
            return new NotifyreService($app->make(NotifyreDriverFactoryInterface::class));
        });

        // Register the helper alias
        $this->app->singleton('notifyre', function ($app) {
            return $app->make(NotifyreServiceInterface::class);
        });
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

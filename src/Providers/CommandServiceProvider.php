<?php

namespace MagicSystemsIO\Notifyre\Providers;

use Illuminate\Support\ServiceProvider;
use MagicSystemsIO\Notifyre\Commands\NotifyreSmsListCommand;
use MagicSystemsIO\Notifyre\Commands\NotifyreSmsSendCommand;
use MagicSystemsIO\Notifyre\Commands\PublishNotifyreAllCommand;
use MagicSystemsIO\Notifyre\Commands\PublishNotifyreConfigCommand;
use MagicSystemsIO\Notifyre\Commands\PublishNotifyreEnvCommand;

class CommandServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                NotifyreSmsSendCommand::class,
                NotifyreSmsListCommand::class,
                PublishNotifyreAllCommand::class,
                PublishNotifyreConfigCommand::class,
                PublishNotifyreEnvCommand::class,
            ]);
        }
    }
}

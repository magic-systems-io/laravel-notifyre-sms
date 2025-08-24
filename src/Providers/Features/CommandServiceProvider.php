<?php

namespace MagicSystemsIO\Notifyre\Providers\Features;

use Illuminate\Support\ServiceProvider;
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
                PublishNotifyreAllCommand::class,
                PublishNotifyreConfigCommand::class,
                PublishNotifyreEnvCommand::class,
                NotifyreSmsSendCommand::class,
            ]);
        }
    }
}

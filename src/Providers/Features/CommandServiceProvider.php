<?php

namespace Arbi\Notifyre\Providers\Features;

use Arbi\Notifyre\Commands\NotifyreSmsSendCommand;
use Arbi\Notifyre\Commands\PublishNotifyreAllCommand;
use Arbi\Notifyre\Commands\PublishNotifyreConfigCommand;
use Arbi\Notifyre\Commands\PublishNotifyreEnvCommand;
use Illuminate\Support\ServiceProvider;

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

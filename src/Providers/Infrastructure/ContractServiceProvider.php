<?php

namespace MagicSystemsIO\Notifyre\Providers\Infrastructure;

use Illuminate\Support\ServiceProvider;
use MagicSystemsIO\Notifyre\Contracts\NotifyreDriverFactoryInterface;
use MagicSystemsIO\Notifyre\Contracts\NotifyreServiceInterface;
use MagicSystemsIO\Notifyre\Services\DriverFactory;
use MagicSystemsIO\Notifyre\Services\NotifyreService;

class ContractServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(NotifyreDriverFactoryInterface::class, DriverFactory::class);
        $this->app->bind(NotifyreServiceInterface::class, NotifyreService::class);
    }
}

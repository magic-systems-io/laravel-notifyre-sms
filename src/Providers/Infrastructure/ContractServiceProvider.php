<?php

namespace Arbi\Notifyre\Providers\Infrastructure;

use Arbi\Notifyre\Contracts\NotifyreApiClientInterface;
use Arbi\Notifyre\Contracts\NotifyreDriverFactoryInterface;
use Arbi\Notifyre\Contracts\NotifyreServiceInterface;
use Arbi\Notifyre\Services\DriverFactory;
use Arbi\Notifyre\Services\NotifyreApiClient;
use Arbi\Notifyre\Services\NotifyreService;
use Illuminate\Support\ServiceProvider;

class ContractServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(NotifyreApiClientInterface::class, NotifyreApiClient::class);
        $this->app->bind(NotifyreDriverFactoryInterface::class, DriverFactory::class);
        $this->app->bind(NotifyreServiceInterface::class, NotifyreService::class);
    }
}

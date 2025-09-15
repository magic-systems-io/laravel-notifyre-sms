<?php

use MagicSystemsIO\Notifyre\Contracts\NotifyreManager;
use MagicSystemsIO\Notifyre\Providers\ServicesServiceProvider;
use MagicSystemsIO\Notifyre\Services\NotifyreService;

it('binds NotifyreManager to NotifyreService as a singleton', function () {
    $provider = new ServicesServiceProvider($this->app);

    $provider->register();

    $a = app(NotifyreManager::class);
    $b = app(NotifyreManager::class);

    expect($a)->toBeInstanceOf(NotifyreService::class)
        ->and($a)->toBe($b);
});

it('resolves NotifyreService and it implements the contract', function () {
    $provider = new ServicesServiceProvider($this->app);
    $provider->register();

    $service = app(NotifyreManager::class);

    expect($service)->toBeInstanceOf(NotifyreManager::class)
        ->and($service)->toBeInstanceOf(NotifyreService::class);
});

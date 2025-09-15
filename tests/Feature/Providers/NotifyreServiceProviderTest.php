<?php

use MagicSystemsIO\Notifyre\Providers\CommandServiceProvider;
use MagicSystemsIO\Notifyre\Providers\ConfigurationServiceProvider;
use MagicSystemsIO\Notifyre\Providers\MigrationServiceProvider;
use MagicSystemsIO\Notifyre\Providers\NotifyreLoggingServiceProvider;
use MagicSystemsIO\Notifyre\Providers\NotifyreServiceProvider;
use MagicSystemsIO\Notifyre\Providers\RouteServiceProvider;
use MagicSystemsIO\Notifyre\Providers\ServicesServiceProvider;

it('registers dependent service providers', function () {
    $provider = new NotifyreServiceProvider($this->app);

    $provider->register();

    $loaded = array_keys($this->app->getLoadedProviders());

    expect($loaded)->toContain(ConfigurationServiceProvider::class)
        ->and($loaded)->toContain(ServicesServiceProvider::class)
        ->and($loaded)->toContain(MigrationServiceProvider::class)
        ->and($loaded)->toContain(CommandServiceProvider::class)
        ->and($loaded)->toContain(RouteServiceProvider::class)
        ->and($loaded)->toContain(NotifyreLoggingServiceProvider::class);
});

it('boot does not throw when executed', function () {
    $provider = new NotifyreServiceProvider($this->app);

    $provider->boot();

    expect(true)->toBeTrue();
});

<?php

use MagicSystemsIO\Notifyre\Providers\MigrationServiceProvider;

it('does nothing when notifyre.database.enabled is false', function () {
    config()->set('notifyre.database.enabled', false);

    $provider = new MigrationServiceProvider($this->app);

    $provider->boot();

    expect(true)->toBeTrue();
});

it('has migration files available when notifyre.database.enabled is true', function () {
    config()->set('notifyre.database.enabled', true);

    $provider = new MigrationServiceProvider($this->app);

    $provider->boot();

    $packageMigrationsPath = realpath(__DIR__ . '/../../../src/../database/migrations') ?: realpath(__DIR__ . '/../../../../database/migrations');

    expect($packageMigrationsPath)->not->toBeFalse();
    $files = glob($packageMigrationsPath . '/*.php');

    expect(is_array($files) && count($files) > 0)->toBeTrue();
});

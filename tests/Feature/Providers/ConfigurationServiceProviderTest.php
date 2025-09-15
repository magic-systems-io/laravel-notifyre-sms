<?php

use MagicSystemsIO\Notifyre\Providers\ConfigurationServiceProvider;

it('merges package configuration into the application', function () {
    expect(config('notifyre'))->toBeArray()
        ->and(config('notifyre.driver'))->toBe('sms')
        ->and(config('notifyre.http.base_url'))->toBe('https://api.notifyre.com');
});

it('does not overwrite existing application config when registering provider again', function () {
    config()->set('notifyre.driver', 'custom-driver');

    $provider = new ConfigurationServiceProvider($this->app);
    $provider->register();

    expect(config('notifyre.driver'))->toBe('custom-driver');
});

<?php

use MagicSystemsIO\Notifyre\Providers\NotifyreLoggingServiceProvider;
use MagicSystemsIO\Notifyre\Services\NotifyreLogger;
use Monolog\Logger as MonologLogger;

it('registers NotifyreLogger as a singleton', function () {
    $provider = new NotifyreLoggingServiceProvider($this->app);

    $provider->register();

    $a = app(NotifyreLogger::class);
    $b = app(NotifyreLogger::class);

    expect($a)->toBeInstanceOf(NotifyreLogger::class)
        ->and($a)->toBe($b);
});

it('boot does not throw and the logger is invokable returning a Monolog Logger', function () {
    $provider = new NotifyreLoggingServiceProvider($this->app);

    $provider->register();
    $provider->boot();

    $instance = app(NotifyreLogger::class);

    $monolog = $instance([]);

    expect($monolog)->toBeInstanceOf(MonologLogger::class);
});

it('initializes and creates a notifyre_sms logging channel in config when used', function () {
    config()->set('logging.channels.notifyre_sms');

    $prefix = NotifyreLogger::getPrefix();

    $channelConfig = config('logging.channels.notifyre_sms');

    expect($prefix)->toBeString()
        ->and($channelConfig)->toBeArray()
        ->and(array_key_exists('path', $channelConfig))->toBeTrue();
});

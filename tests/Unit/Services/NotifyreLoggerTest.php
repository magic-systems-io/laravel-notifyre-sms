<?php

use Illuminate\Support\Facades\Log;
use MagicSystemsIO\Notifyre\Services\NotifyreLogger;
use Monolog\Logger as MonologLogger;

beforeEach(function () {
    config()->set('notifyre.logging.enabled', true);
    config()->set('notifyre.logging.prefix', 'notifyre_sms');

    $reflection = new ReflectionClass(NotifyreLogger::class);
    $initializedProperty = $reflection->getProperty('initialized');
    $initializedProperty->setValue(null, false);
});

afterEach(function () {
    Mockery::close();
});

it('returns configured prefix', function () {
    config()->set('notifyre.logging.prefix', 'custom_prefix');

    expect(NotifyreLogger::getPrefix())->toBe('custom_prefix');
});

it('does nothing when logging is disabled', function () {
    config()->set('notifyre.logging.enabled', false);

    Log::shouldReceive('channel')->never();
    Log::shouldReceive('log')->never();

    NotifyreLogger::info('should not be logged');

    expect(true)->toBeTrue();
});

it('logs to custom channel with prefix when enabled', function () {
    config()->set('notifyre.logging.enabled', true);
    config()->set('notifyre.logging.prefix', 'myprefix');

    $channelMock = Mockery::mock();
    $channelMock->shouldReceive('log')->once()->with('info', '[myprefix] Test message', []);

    Log::shouldReceive('channel')->once()->with('notifyre_sms')->andReturn($channelMock);

    NotifyreLogger::info('Test message');
});

it('__invoke returns a Monolog logger with handlers', function () {
    $logger = (new NotifyreLogger())([]);

    expect($logger)->toBeInstanceOf(MonologLogger::class)
        ->and(count($logger->getHandlers()))->toBeGreaterThanOrEqual(1);
});

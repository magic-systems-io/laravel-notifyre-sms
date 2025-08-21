<?php

namespace Arbi\Notifyre\Tests\Feature;

use Arbi\Notifyre\Contracts\NotifyreServiceInterface;
use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Arbi\Notifyre\Enums\NotifyreDriver;
use Arbi\Notifyre\Services\DriverFactory;
use Arbi\Notifyre\Services\Drivers\LogDriver;
use Arbi\Notifyre\Services\Drivers\SMSDriver;
use Arbi\Notifyre\Services\NotifyreService;
use Exception;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;

describe('Notifyre Integration', function () {
    beforeEach(function () {
        $this->app = app();
        Config::set('services.notifyre.driver', 'log');
        Config::set('notifyre.base_url', 'https://api.notifyre.com');
        Config::set('services.notifyre.api_key', 'test-api-key');
        Config::set('notifyre.timeout', 30);
        Config::set('notifyre.retry.times', 3);
        Config::set('notifyre.retry.sleep', 1000);
        Config::set('notifyre.cache.enabled', false);
    });

    it('sends SMS through complete flow with log driver', function () {
        $factory = new DriverFactory();
        $service = new NotifyreService($factory);

        $message = new RequestBodyDTO(
            body: 'Integration test message',
            sender: 'TestApp',
            recipients: [
                new Recipient('mobile_number', '+1234567890'),
            ]
        );

        // Should not throw any exceptions
        expect(fn () => $service->send($message))->not->toThrow(Exception::class);
    });

    it('uses helper function to resolve service', function () {
        $service = notifyre();
        expect($service)->toBeInstanceOf(NotifyreServiceInterface::class);

        // Test that the service is properly bound and can be resolved
        expect(true)->toBeTrue();
    });

    it('validates driver configuration through complete flow', function () {
        $this->app['config']->set('services.notifyre.driver', 'invalid_driver');

        $factory = new DriverFactory();

        expect(fn () => $factory->create())->toThrow(InvalidArgumentException::class);
    });

    it('handles configuration priority correctly', function () {
        $this->app['config']->set('notifyre.driver', 'sms');
        $this->app['config']->set('services.notifyre.driver', 'log');

        $factory = new DriverFactory();
        $driver = $factory->create();

        expect($driver)->toBeInstanceOf(LogDriver::class);
    });

    it('creates different drivers based on configuration', function () {
        $factory = new DriverFactory();

        $this->app['config']->set('services.notifyre.driver', 'log');
        $logDriver = $factory->create();
        expect($logDriver)->toBeInstanceOf(LogDriver::class);

        $this->app['config']->set('services.notifyre.driver', 'sms');
        $smsDriver = $factory->create();
        expect($smsDriver)->toBeInstanceOf(SMSDriver::class);
    });

    it('handles enum values correctly', function () {
        expect(NotifyreDriver::LOG->value)->toBe('log')
            ->and(NotifyreDriver::SMS->value)->toBe('sms')
            ->and(NotifyreDriver::values())->toBe(['sms', 'log'])
            ->and(NotifyreDriver::isValid('log'))->toBeTrue()
            ->and(NotifyreDriver::isValid('sms'))->toBeTrue()
            ->and(NotifyreDriver::isValid('invalid'))->toBeFalse();
    });
});

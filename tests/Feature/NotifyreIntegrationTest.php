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
        // Reset to default configuration
        Config::set('notifyre.driver', 'log');
        Config::set('services.notifyre.driver', null);
    });

    afterEach(function () {
        // Clean up configuration
        Config::set('notifyre.driver', 'log');
        Config::set('services.notifyre.driver', null);
    });

    it('sends SMS through complete flow with log driver', function () {
        $service = new NotifyreService(new DriverFactory());

        $message = new RequestBodyDTO(
            body: 'Integration test message',
            from: 'TestApp',
            recipients: [
                new Recipient('virtual_mobile_number', '+1234567890'),
            ]
        );

        expect(fn () => $service->send($message))->not->toThrow(Exception::class);
    });

    it('uses helper function to resolve service', function () {
        expect(notifyre())->toBeInstanceOf(NotifyreServiceInterface::class)
            ->and(true)->toBeTrue();
    });

    it('validates driver configuration through complete flow', function () {
        Config::set('notifyre.driver', 'invalid_driver');
        Config::set('services.notifyre.driver', 'invalid_driver');

        expect(fn () => new DriverFactory()->create())->toThrow(InvalidArgumentException::class);
    });

    it('handles configuration priority correctly', function () {
        Config::set('services.notifyre.driver', 'sms');
        Config::set('notifyre.driver', 'log');

        expect(new DriverFactory()->create())->toBeInstanceOf(SMSDriver::class);
    });

    it('creates different drivers based on configuration', function () {
        Config::set('notifyre.driver', 'log');
        Config::set('services.notifyre.driver', null);
        expect(new DriverFactory()->create())->toBeInstanceOf(LogDriver::class);

        Config::set('notifyre.driver', 'sms');
        Config::set('services.notifyre.driver', null);
        expect(new DriverFactory()->create())->toBeInstanceOf(SMSDriver::class);
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

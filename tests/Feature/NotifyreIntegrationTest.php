<?php

namespace MagicSystemsIO\Notifyre\Tests\Feature;

use Exception;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;
use MagicSystemsIO\Notifyre\Contracts\NotifyreServiceInterface;
use MagicSystemsIO\Notifyre\DTO\SMS\Recipient;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBodyDTO;
use MagicSystemsIO\Notifyre\Enums\NotifyreDriver;
use MagicSystemsIO\Notifyre\Services\DriverFactory;
use MagicSystemsIO\Notifyre\Services\Drivers\LogDriver;
use MagicSystemsIO\Notifyre\Services\Drivers\SMSDriver;
use MagicSystemsIO\Notifyre\Services\NotifyreService;

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
            body:       'Integration test message',
            sender:     'TestApp',
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

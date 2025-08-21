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
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;

describe('Notifyre Integration', function () {
    beforeEach(function () {
        Config::set('notifyre.driver', 'log');
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

        $service->send($message);

        expect(true)->toBeTrue();
    });

    it('sends SMS through complete flow with SMS driver', function () {
        Config::set('notifyre.driver', 'sms');

        $factory = new DriverFactory();
        $service = new NotifyreService($factory);

        $message = new RequestBodyDTO(
            body: 'Integration test message',
            sender: 'TestApp',
            recipients: [
                new Recipient('mobile_number', '+1234567890'),
            ]
        );

        expect(fn () => $service->send($message))->toThrow(RequestException::class);
    });

    it('uses helper function to send SMS', function () {
        $message = new RequestBodyDTO(
            body: 'Helper function test',
            sender: 'TestApp',
            recipients: [
                new Recipient('mobile_number', '+1234567890'),
            ]
        );

        $service = notifyre();
        expect($service)->toBeInstanceOf(NotifyreServiceInterface::class);

        $service->send($message);

        expect(true)->toBeTrue();
    });

    it('handles multiple recipients through complete flow', function () {
        $factory = new DriverFactory();
        $service = new NotifyreService($factory);

        $message = new RequestBodyDTO(
            body: 'Multi-recipient test',
            sender: 'TestApp',
            recipients: [
                new Recipient('mobile_number', '+1234567890'),
                new Recipient('contact', 'contact123'),
                new Recipient('group', 'group456'),
            ]
        );

        $service->send($message);

        expect(true)->toBeTrue();
    });

    it('handles empty sender through complete flow', function () {
        $factory = new DriverFactory();
        $service = new NotifyreService($factory);

        $message = new RequestBodyDTO(
            body: 'No sender test',
            sender: null,
            recipients: [
                new Recipient('mobile_number', '+1234567890'),
            ]
        );

        $service->send($message);

        expect(true)->toBeTrue();
    });

    it('validates driver configuration through complete flow', function () {
        Config::set('notifyre.driver', 'invalid_driver');

        $factory = new DriverFactory();

        expect(fn () => $factory->create())->toThrow(InvalidArgumentException::class);
    });

    it('handles configuration priority correctly', function () {
        Config::set('notifyre.driver', 'sms');
        Config::set('services.notifyre.driver', 'log');

        $factory = new DriverFactory();
        $driver = $factory->create();

        expect($driver)->toBeInstanceOf(LogDriver::class);
    });

    it('creates different drivers based on configuration', function () {
        Config::set('notifyre.driver', 'log');
        $factory = new DriverFactory();
        $logDriver = $factory->create();
        expect($logDriver)->toBeInstanceOf(LogDriver::class);

        Config::set('notifyre.driver', 'sms');
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

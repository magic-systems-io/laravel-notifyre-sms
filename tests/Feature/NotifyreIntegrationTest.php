<?php

use Arbi\Notifyre\Services\NotifyreService;
use Arbi\Notifyre\Services\DriverFactory;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\Enums\NotifyreDriver;
use Illuminate\Support\Facades\Config;

describe('Notifyre Integration', function () {
    beforeEach(function () {
        // Set up test configuration
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

        // This should log to Laravel logs instead of sending actual SMS
        $service->send($message);

        // If we get here without exceptions, the flow worked
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

        // This should attempt to send via HTTP (will fail in test environment)
        expect(fn() => $service->send($message))
            ->toThrow(\Illuminate\Http\Client\ConnectionException::class);
    });

    it('uses helper function to send SMS', function () {
        $message = new RequestBodyDTO(
            body: 'Helper function test',
            sender: 'TestApp',
            recipients: [
                new Recipient('mobile_number', '+1234567890'),
            ]
        );

        // Test the helper function
        $service = notifyre();
        expect($service)->toBeInstanceOf(NotifyreService::class);

        // This should work with log driver
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

        expect(fn() => $factory->create())
            ->toThrow(\Arbi\Notifyre\Exceptions\InvalidConfigurationException::class);
    });

    it('handles configuration priority correctly', function () {
        Config::set('notifyre.driver', 'sms');
        Config::set('services.notifyre.driver', 'log');

        $factory = new DriverFactory();
        $driver = $factory->create();

        // Should use services.notifyre.driver (log) instead of notifyre.driver (sms)
        expect($driver)->toBeInstanceOf(\Arbi\Notifyre\Services\Drivers\LogDriver::class);
    });

    it('creates different drivers based on configuration', function () {
        // Test log driver
        Config::set('notifyre.driver', 'log');
        $factory = new DriverFactory();
        $logDriver = $factory->create();
        expect($logDriver)->toBeInstanceOf(\Arbi\Notifyre\Services\Drivers\LogDriver::class);

        // Test SMS driver
        Config::set('notifyre.driver', 'sms');
        $smsDriver = $factory->create();
        expect($smsDriver)->toBeInstanceOf(\Arbi\Notifyre\Services\Drivers\SMSDriver::class);
    });

    it('handles enum values correctly', function () {
        expect(NotifyreDriver::LOG->value)->toBe('log');
        expect(NotifyreDriver::SMS->value)->toBe('sms');
        expect(NotifyreDriver::values())->toBe(['sms', 'log']);
        expect(NotifyreDriver::isValid('log'))->toBeTrue();
        expect(NotifyreDriver::isValid('sms'))->toBeTrue();
        expect(NotifyreDriver::isValid('invalid'))->toBeFalse();
    });
});

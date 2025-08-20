<?php

use Arbi\Notifyre\Services\DriverFactory;
use Arbi\Notifyre\Services\Drivers\LogDriver;
use Arbi\Notifyre\Services\Drivers\SMSDriver;
use Arbi\Notifyre\Enums\NotifyreDriver;
use Arbi\Notifyre\Exceptions\InvalidConfigurationException;

describe('DriverFactory', function () {
    beforeEach(function () {
        // Clear any existing config
        config()->set('services.notifyre.driver', null);
        config()->set('notifyre.driver', null);
    });

    it('creates log driver when configured', function () {
        config()->set('notifyre.driver', 'log');

        $factory = new DriverFactory();
        $driver = $factory->create();

        expect($driver)->toBeInstanceOf(LogDriver::class);
    });

    it('creates SMS driver when configured', function () {
        config()->set('notifyre.driver', 'sms');

        $factory = new DriverFactory();
        $driver = $factory->create();

        expect($driver)->toBeInstanceOf(SMSDriver::class);
    });

    it('prioritizes services.notifyre.driver over notifyre.driver', function () {
        config()->set('notifyre.driver', 'sms');
        config()->set('services.notifyre.driver', 'log');

        $factory = new DriverFactory();
        $driver = $factory->create();

        expect($driver)->toBeInstanceOf(LogDriver::class);
    });

    it('falls back to notifyre.driver when services.notifyre.driver is not set', function () {
        config()->set('notifyre.driver', 'sms');

        $factory = new DriverFactory();
        $driver = $factory->create();

        expect($driver)->toBeInstanceOf(SMSDriver::class);
    });

    it('throws exception for invalid driver', function () {
        config()->set('notifyre.driver', 'invalid_driver');

        $factory = new DriverFactory();

        expect(fn() => $factory->create())
            ->toThrow(InvalidConfigurationException::class, "Invalid Notifyre driver 'invalid_driver'. Supported drivers are: sms, log");
    });

    it('throws exception for empty driver', function () {
        config()->set('notifyre.driver', '');

        $factory = new DriverFactory();

        expect(fn() => $factory->create())
            ->toThrow(InvalidConfigurationException::class, "Invalid Notifyre driver ''. Supported drivers are: sms, log");
    });

    it('throws exception for null driver', function () {
        // No config set, should default to null

        $factory = new DriverFactory();

        expect(fn() => $factory->create())
            ->toThrow(InvalidConfigurationException::class, "Invalid Notifyre driver ''. Supported drivers are: sms, log");
    });

    it('throws exception for whitespace-only driver', function () {
        config()->set('notifyre.driver', '   ');

        $factory = new DriverFactory();

        expect(fn() => $factory->create())
            ->toThrow(InvalidConfigurationException::class, "Invalid Notifyre driver '   '. Supported drivers are: sms, log");
    });

    it('creates drivers with case-sensitive matching', function () {
        config()->set('notifyre.driver', 'SMS'); // Uppercase

        $factory = new DriverFactory();

        expect(fn() => $factory->create())
            ->toThrow(InvalidConfigurationException::class, "Invalid Notifyre driver 'SMS'. Supported drivers are: sms, log");
    });

    it('creates log driver with exact match', function () {
        config()->set('notifyre.driver', NotifyreDriver::LOG->value);

        $factory = new DriverFactory();
        $driver = $factory->create();

        expect($driver)->toBeInstanceOf(LogDriver::class);
    });

    it('creates SMS driver with exact match', function () {
        config()->set('notifyre.driver', NotifyreDriver::SMS->value);

        $factory = new DriverFactory();
        $driver = $factory->create();

        expect($driver)->toBeInstanceOf(SMSDriver::class);
    });
});

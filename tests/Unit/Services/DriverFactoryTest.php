<?php

namespace Arbi\Notifyre\Tests\Unit\Services;

use Arbi\Notifyre\Enums\NotifyreDriver;
use Arbi\Notifyre\Services\DriverFactory;
use Arbi\Notifyre\Services\Drivers\LogDriver;
use Arbi\Notifyre\Services\Drivers\SMSDriver;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;

describe('DriverFactory', function () {
    beforeEach(function () {
        Config::set('services.notifyre.driver');
        Config::set('notifyre.driver');
    });

    it('creates log driver when configured', function () {
        Config::set('notifyre.driver', 'log');

        $factory = new DriverFactory();
        $driver = $factory->create();

        expect($driver)->toBeInstanceOf(LogDriver::class);
    });

    it('creates SMS driver when configured', function () {
        Config::set('notifyre.driver', 'sms');

        $factory = new DriverFactory();
        $driver = $factory->create();

        expect($driver)->toBeInstanceOf(SMSDriver::class);
    });

    it('prioritizes services.notifyre.driver over notifyre.driver', function () {
        Config::set('notifyre.driver', 'sms');
        Config::set('services.notifyre.driver', 'log');

        $factory = new DriverFactory();
        $driver = $factory->create();

        expect($driver)->toBeInstanceOf(LogDriver::class);
    });

    it('falls back to notifyre.driver when services.notifyre.driver is not set', function () {
        Config::set('notifyre.driver', 'sms');

        $factory = new DriverFactory();
        $driver = $factory->create();

        expect($driver)->toBeInstanceOf(SMSDriver::class);
    });

    it('throws exception for invalid driver', function () {
        Config::set('notifyre.driver', 'invalid_driver');

        $factory = new DriverFactory();

        expect(fn () => $factory->create())
            ->toThrow(InvalidArgumentException::class, "Invalid Notifyre driver 'invalid_driver'. Supported drivers are: sms, log");
    });

    it('throws exception for empty driver', function () {
        Config::set('notifyre.driver', '');

        $factory = new DriverFactory();

        expect(fn () => $factory->create())
            ->toThrow(InvalidArgumentException::class, "Invalid Notifyre driver ''. Supported drivers are: sms, log");
    });

    it('throws exception for null driver', function () {
        $factory = new DriverFactory();

        expect(fn () => $factory->create())
            ->toThrow(InvalidArgumentException::class, "Invalid Notifyre driver ''. Supported drivers are: sms, log");
    });

    it('throws exception for whitespace-only driver', function () {
        Config::set('notifyre.driver', '   ');

        $factory = new DriverFactory();

        expect(fn () => $factory->create())
            ->toThrow(InvalidArgumentException::class, "Invalid Notifyre driver '   '. Supported drivers are: sms, log");
    });

    it('creates drivers with case-sensitive matching', function () {
        Config::set('notifyre.driver', 'SMS');

        $factory = new DriverFactory();

        expect(fn () => $factory->create())
            ->toThrow(InvalidArgumentException::class, "Invalid Notifyre driver 'SMS'. Supported drivers are: sms, log");
    });

    it('creates log driver with exact match', function () {
        Config::set('notifyre.driver', NotifyreDriver::LOG->value);

        $factory = new DriverFactory();
        $driver = $factory->create();

        expect($driver)->toBeInstanceOf(LogDriver::class);
    });

    it('creates SMS driver with exact match', function () {
        Config::set('notifyre.driver', NotifyreDriver::SMS->value);

        $factory = new DriverFactory();
        $driver = $factory->create();

        expect($driver)->toBeInstanceOf(SMSDriver::class);
    });
});

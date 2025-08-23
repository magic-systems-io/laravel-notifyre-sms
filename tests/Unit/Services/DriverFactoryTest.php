<?php

namespace MagicSystemsIO\Notifyre\Tests\Unit\Services;

use Illuminate\Support\Facades\Config;
use InvalidArgumentException;
use MagicSystemsIO\Notifyre\Enums\NotifyreDriver;
use MagicSystemsIO\Notifyre\Services\DriverFactory;
use MagicSystemsIO\Notifyre\Services\Drivers\LogDriver;
use MagicSystemsIO\Notifyre\Services\Drivers\SMSDriver;

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
        $this->app['config']->set('services.notifyre.driver', 'sms');

        $factory = new DriverFactory();
        $driver = $factory->create();

        expect($driver)->toBeInstanceOf(SMSDriver::class);
    });

    it('prioritizes services.notifyre.driver over notifyre.driver', function () {
        $this->app['config']->set('notifyre.driver', 'sms');
        $this->app['config']->set('services.notifyre.driver', 'log');

        $factory = new DriverFactory();
        $driver = $factory->create();

        expect($driver)->toBeInstanceOf(LogDriver::class);
    });

    it('falls back to notifyre.driver when services.notifyre.driver is not set', function () {
        $this->app['config']->set('services.notifyre.driver', null);
        $this->app['config']->set('notifyre.driver', 'sms');

        $factory = new DriverFactory();
        $driver = $factory->create();

        expect($driver)->toBeInstanceOf(SMSDriver::class);
    });

    it('throws exception for invalid driver', function () {
        $this->app['config']->set('services.notifyre.driver', 'invalid_driver');

        $factory = new DriverFactory();

        expect(fn () => $factory->create())
            ->toThrow(InvalidArgumentException::class, "Invalid Notifyre driver 'invalid_driver'. Supported drivers are: sms, log");
    });

    it('throws exception for empty driver', function () {
        $this->app['config']->set('services.notifyre.driver', '');

        $factory = new DriverFactory();

        expect(fn () => $factory->create())
            ->toThrow(InvalidArgumentException::class, "Invalid Notifyre driver ''. Supported drivers are: sms, log");
    });

    it('throws exception for null driver', function () {
        $this->app['config']->set('services.notifyre.driver', null);
        $this->app['config']->set('notifyre.driver', null);

        $factory = new DriverFactory();

        expect(fn () => $factory->create())
            ->toThrow(InvalidArgumentException::class, "Invalid Notifyre driver ''. Supported drivers are: sms, log");
    });

    it('throws exception for whitespace-only driver', function () {
        $this->app['config']->set('services.notifyre.driver', '   ');

        $factory = new DriverFactory();

        expect(fn () => $factory->create())
            ->toThrow(InvalidArgumentException::class, "Invalid Notifyre driver '   '. Supported drivers are: sms, log");
    });

    it('creates drivers with case-sensitive matching', function () {
        $this->app['config']->set('services.notifyre.driver', 'SMS');

        $factory = new DriverFactory();

        expect(fn () => $factory->create())
            ->toThrow(InvalidArgumentException::class, "Invalid Notifyre driver 'SMS'. Supported drivers are: sms, log");
    });

    it('creates log driver with exact match', function () {
        $this->app['config']->set('services.notifyre.driver', NotifyreDriver::LOG->value);

        $factory = new DriverFactory();
        $driver = $factory->create();

        expect($driver)->toBeInstanceOf(LogDriver::class);
    });

    it('creates SMS driver with exact match', function () {
        $this->app['config']->set('services.notifyre.driver', NotifyreDriver::SMS->value);

        $factory = new DriverFactory();
        $driver = $factory->create();

        expect($driver)->toBeInstanceOf(SMSDriver::class);
    });
});

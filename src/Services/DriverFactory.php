<?php

namespace Arbi\Notifyre\Services;

use Arbi\Notifyre\Contracts\NotifyreDriverInterface;
use Arbi\Notifyre\Enums\NotifyreDriver;
use Arbi\Notifyre\Exceptions\InvalidConfigurationException;
use Arbi\Notifyre\Services\Drivers\LogDriver;
use Arbi\Notifyre\Services\Drivers\SMSDriver;

class DriverFactory
{
    /**
     * @throws InvalidConfigurationException
     */
    public function create(): NotifyreDriverInterface
    {
        $driver = config('services.notifyre.driver') ?? config('notifyre.driver');

        return match ($driver) {
            NotifyreDriver::LOG->value => new LogDriver(),
            NotifyreDriver::SMS->value => new SMSDriver(),
            default => throw new InvalidConfigurationException("Invalid Notifyre driver '$driver'. Supported drivers are: " . implode(', ', NotifyreDriver::values())),
        };
    }
}

<?php

namespace MagicSystemsIO\Notifyre\Services;

use InvalidArgumentException;
use MagicSystemsIO\Notifyre\Contracts\NotifyreDriverFactoryInterface;
use MagicSystemsIO\Notifyre\Contracts\NotifyreDriverInterface;
use MagicSystemsIO\Notifyre\Enums\NotifyreDriver;
use MagicSystemsIO\Notifyre\Services\Drivers\LogDriver;
use MagicSystemsIO\Notifyre\Services\Drivers\SMSDriver;

readonly class DriverFactory implements NotifyreDriverFactoryInterface
{
    /**
     * @throws InvalidArgumentException
     */
    public function create(): NotifyreDriverInterface
    {
        $driver = config('services.notifyre.driver') ?? config('notifyre.driver');

        if (empty(trim($driver ?? ''))) {
            throw new InvalidArgumentException("Invalid Notifyre driver '$driver'. Supported drivers are: " . implode(', ', NotifyreDriver::values()));
        }

        return match ($driver) {
            NotifyreDriver::LOG->value => new LogDriver(),
            NotifyreDriver::SMS->value => new SMSDriver(),
            default => throw new InvalidArgumentException("Invalid Notifyre driver '$driver'. Supported drivers are: " . implode(', ', NotifyreDriver::values())),
        };
    }
}

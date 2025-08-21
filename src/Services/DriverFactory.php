<?php

namespace Arbi\Notifyre\Services;

use Arbi\Notifyre\Contracts\NotifyreDriverFactoryInterface;
use Arbi\Notifyre\Contracts\NotifyreDriverInterface;
use Arbi\Notifyre\Enums\NotifyreDriver;
use Arbi\Notifyre\Services\Drivers\LogDriver;
use Arbi\Notifyre\Services\Drivers\SMSDriver;
use InvalidArgumentException;

readonly class DriverFactory implements NotifyreDriverFactoryInterface
{
    /**
     * @throws InvalidArgumentException
     */
    public function create(): NotifyreDriverInterface
    {
        $driver = config('services.notifyre.driver') ?? config('notifyre.driver');

        return match ($driver) {
            NotifyreDriver::LOG->value => new LogDriver(),
            NotifyreDriver::SMS->value => new SMSDriver(),
            default => throw new InvalidArgumentException("Invalid Notifyre driver '$driver'. Supported drivers are: " . implode(', ', NotifyreDriver::values())),
        };
    }
}

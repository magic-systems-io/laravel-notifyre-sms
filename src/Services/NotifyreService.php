<?php

namespace MagicSystemsIO\Notifyre\Services;

use Illuminate\Http\Client\ConnectionException;
use InvalidArgumentException;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponseBody;
use MagicSystemsIO\Notifyre\Enums\NotifyreDriver;
use MagicSystemsIO\Notifyre\Services\Drivers\LogDriver;
use MagicSystemsIO\Notifyre\Services\Drivers\SMSDriver;

readonly class NotifyreService
{
    /**
     * Send SMS directly using the service
     *
     * @throws InvalidArgumentException
     * @throws ConnectionException
     */
    public function send(RequestBody $message): ?ResponseBody
    {
        return $this->create()->send($message);
    }

    private function create(): LogDriver|SMSDriver
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

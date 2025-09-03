<?php

namespace MagicSystemsIO\Notifyre\Services;

use Illuminate\Http\Client\ConnectionException;
use InvalidArgumentException;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
use MagicSystemsIO\Notifyre\Enums\NotifyreDriver;
use MagicSystemsIO\Notifyre\Http\Services\SMSMessagePersister;
use MagicSystemsIO\Notifyre\Services\Drivers\LogDriver;
use MagicSystemsIO\Notifyre\Services\Drivers\SMSDriver;
use Throwable;

class NotifyreService
{
    /**
     * Send SMS using the configured driver
     *
     * @throws InvalidArgumentException
     * @throws ConnectionException
     * @throws Throwable
     */
    public static function send(RequestBody $request): array
    {
        $driverName = self::getDriverName();
        $driver = self::createDriver($driverName);
        $response = $driver->send($request);

        if (empty($response)) {
            return [
                'message' => "Message sent via the $driverName driver",
                'request' => $request->toArray(),
                'response' => $response,
            ];
        }

        if (config('notifyre.api.database.enabled')) {
            return SMSMessagePersister::persist($request, $response);
        }

        return $response->toArray();
    }

    /**
     * Get the configured driver name
     */
    private static function getDriverName(): string
    {
        $driver = trim(config('services.notifyre.driver') ?? config('notifyre.driver'));

        if (empty($driver) || !NotifyreDriver::isValid($driver)) {
            $supportedDrivers = implode(', ', NotifyreDriver::values());
            throw new InvalidArgumentException(
                "Invalid Notifyre driver '$driver'. Supported drivers are: $supportedDrivers"
            );
        }

        return $driver;
    }

    /**
     * Create driver instance based on driver name
     */
    private static function createDriver(string $driver): LogDriver|SMSDriver
    {
        return match ($driver) {
            NotifyreDriver::LOG->value => new LogDriver(),
            NotifyreDriver::SMS->value => new SMSDriver(),
        };
    }
}

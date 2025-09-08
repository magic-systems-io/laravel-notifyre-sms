<?php

namespace MagicSystemsIO\Notifyre\Services;

use Illuminate\Http\Client\ConnectionException;
use InvalidArgumentException;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponseBody;
use MagicSystemsIO\Notifyre\Enums\NotifyreDriver;
use MagicSystemsIO\Notifyre\Services\Drivers\SmsDriver;
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
    public static function send(RequestBody $request): void
    {
        try {
            $response = self::createDriver(self::getDriverName())->send($request);

            NotifyreMessagePersister::persist($request, $response);
        } catch (Throwable $e) {
            NotifyreLogger::error("Failed to send SMS: {$e->getMessage()}", ['exception' => $e]);

            throw $e;
        }
    }

    /**
     * Create driver instance based on driver name
     */
    private static function createDriver(string $driver): SmsDriver
    {
        return match ($driver) {
            NotifyreDriver::SMS->value => new SmsDriver(),
        };
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
     * @throws ConnectionException
     */
    public static function get(string $messageId): ?ResponseBody
    {
        try {
            return self::createDriver(self::getDriverName())->get($messageId);
        } catch (ConnectionException $e) {
            NotifyreLogger::error("Failed to retrieve SMS: {$e->getMessage()}", ['exception' => $e]);

            throw $e;
        }
    }

    /**
     * @throws ConnectionException
     * @return ResponseBody[]
     */
    public static function list(array $queryParams = []): array
    {
        try {
            return self::createDriver(self::getDriverName())->list($queryParams) ?? [];
        } catch (ConnectionException $e) {
            NotifyreLogger::error("Failed to list SMS messages: {$e->getMessage()}", ['exception' => $e]);

            throw $e;
        }
    }
}

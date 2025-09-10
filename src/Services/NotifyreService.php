<?php

namespace MagicSystemsIO\Notifyre\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use MagicSystemsIO\Notifyre\Contracts\NotifyreManager;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponseBody;
use MagicSystemsIO\Notifyre\Enums\NotifyreDriver;
use MagicSystemsIO\Notifyre\Services\Drivers\SmsDriver;
use Throwable;

class NotifyreService implements NotifyreManager
{
    /**
     * Send SMS using the configured driver
     *
     * @throws InvalidArgumentException
     * @throws ConnectionException
     * @throws Throwable
     */
    public function send(RequestBody $request): void
    {
        try {
            $response = $this->createDriver($this->getDriverName())->send($request);

            if (!config('notifyre.database.enabled')) {
                return;
            }

            NotifyreMessagePersister::persist($request, $response);
        } catch (Throwable $e) {
            Log::channel('notifyre')->error("Failed to send SMS: {$e->getMessage()}", ['exception' => $e]);

            throw $e;
        }
    }

    /**
     * Create driver instance based on driver name
     */
    private function createDriver(string $driver): SmsDriver
    {
        return match ($driver) {
            NotifyreDriver::SMS->value => new SmsDriver(),
        };
    }

    /**
     * Get the configured driver name
     */
    private function getDriverName(): string
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
    public function get(string $messageId): ?ResponseBody
    {
        try {
            return $this->createDriver($this->getDriverName())->get($messageId);
        } catch (ConnectionException $e) {
            Log::channel('notifyre')->error("Failed to retrieve SMS: {$e->getMessage()}", ['exception' => $e]);

            throw $e;
        }
    }

    /**
     * @throws ConnectionException
     * @return ResponseBody[]
     */
    public function list(array $queryParams = []): array
    {
        try {
            return $this->createDriver($this->getDriverName())->list($queryParams) ?? [];
        } catch (ConnectionException $e) {
            Log::channel('notifyre')->error("Failed to list SMS messages: {$e->getMessage()}", ['exception' => $e]);

            throw $e;
        }
    }
}

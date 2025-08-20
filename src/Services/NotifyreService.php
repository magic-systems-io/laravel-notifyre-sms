<?php

namespace Arbi\Notifyre\Services;

use Arbi\Notifyre\Contracts\NotifyreServiceInterface;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Arbi\Notifyre\Exceptions\InvalidConfigurationException;

readonly class NotifyreService implements NotifyreServiceInterface
{
    public function __construct(
        private DriverFactory $driverFactory
    ) {
    }

    /**
     * Send SMS directly using the service
     *
     * @throws InvalidConfigurationException
     */
    public function send(RequestBodyDTO $message): void
    {
        $driver = $this->driverFactory->create();
        $driver->send($message);
    }
}

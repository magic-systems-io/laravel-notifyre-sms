<?php

namespace Arbi\Notifyre\Services;

use Arbi\Notifyre\Contracts\NotifyreDriverFactoryInterface;
use Arbi\Notifyre\Contracts\NotifyreServiceInterface;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use InvalidArgumentException;

readonly class NotifyreService implements NotifyreServiceInterface
{
    public function __construct(private NotifyreDriverFactoryInterface $driverFactory)
    {
    }

    /**
     * Send SMS directly using the service
     *
     * @throws InvalidArgumentException
     */
    public function send(RequestBodyDTO $message): void
    {
        $driver = $this->driverFactory->create();
        $driver->send($message);
    }
}

<?php

namespace MagicSystemsIO\Notifyre\Services;

use InvalidArgumentException;
use MagicSystemsIO\Notifyre\Contracts\NotifyreDriverFactoryInterface;
use MagicSystemsIO\Notifyre\Contracts\NotifyreServiceInterface;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBodyDTO;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponseBodyDTO;

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
    public function send(RequestBodyDTO $message): ?ResponseBodyDTO
    {
        return $this->driverFactory->create()->send($message);
    }
}

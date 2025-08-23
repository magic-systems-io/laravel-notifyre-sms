<?php

namespace MagicSystemsIO\Notifyre\Contracts;

use InvalidArgumentException;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBodyDTO;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponseBodyDTO;

interface NotifyreServiceInterface
{
    /**
     * Send SMS directly using the service
     *
     * @throws InvalidArgumentException
     */
    public function send(RequestBodyDTO $message): ?ResponseBodyDTO;
}

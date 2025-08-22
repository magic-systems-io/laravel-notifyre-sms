<?php

namespace Arbi\Notifyre\Contracts;

use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Arbi\Notifyre\DTO\SMS\ResponseBodyDTO;
use InvalidArgumentException;

interface NotifyreServiceInterface
{
    /**
     * Send SMS directly using the service
     *
     * @throws InvalidArgumentException
     */
    public function send(RequestBodyDTO $message): ?ResponseBodyDTO;
}

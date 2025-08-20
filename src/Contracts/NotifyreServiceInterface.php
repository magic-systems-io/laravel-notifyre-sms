<?php

namespace Arbi\Notifyre\Contracts;

use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;

interface NotifyreServiceInterface
{
    /**
     * Send SMS directly using the service
     *
     * @throws \Arbi\Notifyre\Exceptions\InvalidConfigurationException
     */
    public function send(RequestBodyDTO $message): void;
}

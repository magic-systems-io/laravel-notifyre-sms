<?php

namespace Arbi\Notifyre\Contracts;

use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;

interface NotifyreDriverInterface
{
    public function send(RequestBodyDTO $requestBody): void;
}

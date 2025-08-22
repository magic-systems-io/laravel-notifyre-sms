<?php

namespace Arbi\Notifyre\Contracts;

use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Arbi\Notifyre\DTO\SMS\ResponseBodyDTO;

interface NotifyreDriverInterface
{
    public function send(RequestBodyDTO $requestBody): ?ResponseBodyDTO;
}

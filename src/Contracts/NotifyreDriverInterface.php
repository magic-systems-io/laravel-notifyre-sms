<?php

namespace MagicSystemsIO\Notifyre\Contracts;

use MagicSystemsIO\Notifyre\DTO\SMS\RequestBodyDTO;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponseBodyDTO;

interface NotifyreDriverInterface
{
    public function send(RequestBodyDTO $requestBody): ?ResponseBodyDTO;
}

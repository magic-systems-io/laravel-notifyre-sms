<?php

namespace MagicSystemsIO\Notifyre\Contracts;

use MagicSystemsIO\Notifyre\DTO\SMS\RequestBodyDTO;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponseBodyDTO;

interface NotifyreManager
{
    public function send(RequestBodyDTO $request): ?ResponseBodyDTO;
}

<?php

namespace MagicSystemsIO\Notifyre\Contracts;

use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponseBody;

interface NotifyreManager
{
    public function send(RequestBody $request): ?ResponseBody;
}

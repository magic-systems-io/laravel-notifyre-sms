<?php

namespace MagicSystemsIO\Notifyre\Contracts;

use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponseBody;

interface NotifyreManager
{
    public function send(RequestBody $request): void;

    public function get(string $messageId): ?ResponseBody;

    public function list(array $queryParams = []): array;
}

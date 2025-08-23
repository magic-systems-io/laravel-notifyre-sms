<?php

namespace MagicSystemsIO\Notifyre\Services\Drivers;

use Illuminate\Support\Facades\Log;
use MagicSystemsIO\Notifyre\Contracts\NotifyreDriverInterface;
use MagicSystemsIO\Notifyre\DTO\SMS\Recipient;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBodyDTO;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponseBodyDTO;

readonly class LogDriver implements NotifyreDriverInterface
{
    public function send(RequestBodyDTO $requestBody): ?ResponseBodyDTO
    {
        Log::info('SMS would be sent via Notifyre', [
            'body' => $requestBody->body,
            'sender' => $requestBody->sender ?: '(auto-assigned by token)',
            'recipients' => array_map(function (Recipient $recipient) {
                return [
                    'type' => $recipient->type,
                    'value' => $recipient->value,
                ];
            }, $requestBody->recipients),
        ]);

        return null;
    }
}

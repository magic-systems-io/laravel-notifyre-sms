<?php

namespace Arbi\Notifyre\Services\Drivers;

use Arbi\Notifyre\Contracts\NotifyreDriverInterface;
use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Arbi\Notifyre\DTO\SMS\ResponseBodyDTO;
use Illuminate\Support\Facades\Log;

readonly class LogDriver implements NotifyreDriverInterface
{
    public function send(RequestBodyDTO $requestBody): ?ResponseBodyDTO
    {
        Log::info('SMS would be sent via Notifyre', [
            'body' => $requestBody->body,
            'sender' => $requestBody->from ?: '(auto-assigned by token)',
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

<?php

namespace Arbi\Notifyre\Services\Drivers;

use Arbi\Notifyre\Contracts\NotifyreDriverInterface;
use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Illuminate\Notifications\Notification;

class LogDriver implements NotifyreDriverInterface
{
    public function send(RequestBodyDTO $requestBody): void
    {
        logger('SMS would be sent via Notifyre', [
            'body' => $requestBody->body,
            'sender' => $requestBody->sender ?: '(auto-assigned by token)',
            'recipients' => array_map(function (Recipient $recipient) {
                return [
                    'type' => $recipient->type,
                    'value' => $recipient->value,
                ];
            }, $requestBody->recipients),
        ]);
    }
}

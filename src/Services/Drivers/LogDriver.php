<?php

namespace MagicSystemsIO\Notifyre\Services\Drivers;

use Illuminate\Support\Facades\Log;
use MagicSystemsIO\Notifyre\Contracts\NotifyreManager;
use MagicSystemsIO\Notifyre\DTO\SMS\Recipient;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;

readonly class LogDriver implements NotifyreManager
{
    public function send(RequestBody $request): null
    {
        Log::info('SMS would be sent via Notifyre', [
            'body' => $request->body,
            'sender' => ($request->sender !== null && !empty(trim($request->sender))) ? $request->sender : '(auto-assigned by token)',
            'recipients' => array_map(function (Recipient $recipient) {
                return [
                    'type' => $recipient->type,
                    'value' => $recipient->value,
                ];
            }, $request->recipients),
        ]);

        return null;
    }
}

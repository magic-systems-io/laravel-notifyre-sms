<?php

namespace MagicSystemsIO\Notifyre\Channels;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Notifications\Notification;
use InvalidArgumentException;
use MagicSystemsIO\Notifyre\Contracts\NotifyreManager;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
use Throwable;

readonly class NotifyreChannel
{
    /**
     * @param object $notifiable
     * @param Notification $notification
     *
     * @throws ConnectionException
     * @throws Throwable
     */
    public function send(object $notifiable, Notification $notification): void
    {
        if (!method_exists($notifiable, 'routeNotificationForNotifyre')) {
            throw new InvalidArgumentException('Notifiable object requires routeNotificationForNotify()');
        }

        if (!method_exists($notification, 'toNotifyre')) {
            throw new InvalidArgumentException('Notification does not have a toNotifyre method.');
        }

        $requestBody = $notification->toNotifyre();
        if (!$requestBody instanceof RequestBody) {
            throw new InvalidArgumentException('Method `toNotifyre` must return RequestBody object.');
        }

        app(NotifyreManager::class)->send($requestBody);
    }
}

<?php

namespace MagicSystemsIO\Notifyre\Channels;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Notifications\Notification;
use InvalidArgumentException;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
use MagicSystemsIO\Notifyre\Services\NotifyreService;

readonly class NotifyreChannel
{
    public function __construct(
        protected NotifyreService $service
    ) {
    }

    /**
     * @throws InvalidArgumentException
     * @throws ConnectionException
     */
    public function send(object $notifiable, Notification $notification): void
    {
        if (!method_exists($notifiable, 'routeNotificationForNotifyre')) {
            throw new InvalidArgumentException('Notifiable object requires routeNotificationForNotify()');
        }

        if (!method_exists($notification, 'toNotifyre')) {
            throw new InvalidArgumentException('Notification does not have a toNotifyre method.');
        }

        $request = $notification->toNotifyre();
        if (!$request instanceof RequestBody) {
            throw new InvalidArgumentException('Method `toNotifyre` must return RequestBodyDTO object.');
        }

        $this->service->send($request);
    }
}

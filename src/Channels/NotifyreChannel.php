<?php

namespace Arbi\Notifyre\Channels;

use Arbi\Notifyre\Contracts\NotifyreDriverFactoryInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Notifications\Notification;
use InvalidArgumentException;

readonly class NotifyreChannel
{
    public function __construct(private NotifyreDriverFactoryInterface $driverFactory)
    {
    }

    /**
     * @throws InvalidArgumentException
     * @throws ConnectionException
     */
    public function send(object $notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toNotifyre')) {
            throw new InvalidArgumentException('Notification does not have a toNotifyre method.');
        }

        $this->driverFactory->create()->send($notification->toNotifyre());
    }
}

<?php

namespace Arbi\Notifyre\Channels;

use Arbi\Notifyre\Exceptions\InvalidConfigurationException;
use Arbi\Notifyre\Services\DriverFactory;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Notifications\Notification;

readonly class NotifyreChannel
{
    public function __construct(private DriverFactory $driverFactory)
    {
    }

    /**
     * @throws InvalidConfigurationException
     * @throws ConnectionException
     */
    public function send(object $notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toNotifyre')) {
            throw new InvalidConfigurationException('Notification does not have a toNotifyre method.');
        }

        $this->driverFactory->create()->send($notification->toNotifyre());
    }
}

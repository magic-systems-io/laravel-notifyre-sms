<?php

namespace MagicSystemsIO\Notifyre\Providers\Features;

use Illuminate\Support\ServiceProvider;
use MagicSystemsIO\Notifyre\Models\JunctionTables\NotifyreSMSMessageRecipient;
use MagicSystemsIO\Notifyre\Models\NotifyreRecipients;
use MagicSystemsIO\Notifyre\Models\NotifyreSMSMessages;

class ModelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind('notifyre.recipients', NotifyreRecipients::class);
        $this->app->bind('notifyre.sms.messages', NotifyreSMSMessages::class);
        $this->app->bind('notifyre.sms.message.recipient', NotifyreSMSMessageRecipient::class);
    }

    public function boot(): void
    {
    }
}

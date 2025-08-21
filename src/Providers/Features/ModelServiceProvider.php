<?php

namespace Arbi\Notifyre\Providers\Features;

use Arbi\Notifyre\Models\JunctionTables\NotifyreSMSMessageRecipient;
use Arbi\Notifyre\Models\NotifyreRecipients;
use Arbi\Notifyre\Models\NotifyreSMSMessages;
use Illuminate\Support\ServiceProvider;

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

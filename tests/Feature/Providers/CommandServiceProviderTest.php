<?php

use MagicSystemsIO\Notifyre\Commands\NotifyreSmsListCommand;
use MagicSystemsIO\Notifyre\Commands\NotifyreSmsSendCommand;

it('registers the sms artisan commands', function () {
    expect($this->app->bound(NotifyreSmsSendCommand::class))->toBeTrue()
        ->and($this->app->bound(NotifyreSmsListCommand::class))->toBeTrue();
});

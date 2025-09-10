<?php

use MagicSystemsIO\Notifyre\Contracts\NotifyreManager;

if (!function_exists('notifyre')) {
    function notifyre(): NotifyreManager
    {
        return app(NotifyreManager::class);
    }
}

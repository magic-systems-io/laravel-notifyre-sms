<?php


use MagicSystemsIO\Notifyre\Contracts\NotifyreManager;
use MagicSystemsIO\Notifyre\Services\NotifyreService;

if (!function_exists('notifyre')) {
    function notifyre(): NotifyreService
    {
        return app(NotifyreManager::class);
    }
}

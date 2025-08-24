<?php


use MagicSystemsIO\Notifyre\Contracts\NotifyreServiceInterface;

if (!function_exists('notifyre')) {
    function notifyre(): NotifyreServiceInterface
    {
        return app('notifyre');
    }
}

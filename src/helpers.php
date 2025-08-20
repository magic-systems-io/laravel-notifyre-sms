<?php


use Arbi\Notifyre\Contracts\NotifyreServiceInterface;

if (!function_exists('notifyre')) {
    function notifyre(): NotifyreServiceInterface
    {
        return app('notifyre');
    }
}

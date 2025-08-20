<?php


use Arbi\Notifyre\Services\NotifyreService;

if (!function_exists('notifyre')) {
    function notifyre(): NotifyreService
    {
        return app('notifyre');
    }
}

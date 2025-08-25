<?php

use Illuminate\Support\Facades\Route;
use MagicSystemsIO\Notifyre\Http\Controllers\NotifyreSMSController;

if (!config('notifyre.api.enabled', false)) {
    return;
}

Route::apiResource('sms', NotifyreSMSController::class)
    ->only(['index', 'show', 'store']);

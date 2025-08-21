<?php

use Arbi\Notifyre\Http\Controllers\NotifyreSMSController;
use Illuminate\Support\Facades\Route;

if (!config('notifyre.api.enabled', false)) {
    return;
}


Route::apiResource('sms', NotifyreSMSController::class)
    ->only(['index', 'show', 'store']);

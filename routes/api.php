<?php

use Illuminate\Support\Facades\Route;
use MagicSystemsIO\Notifyre\Http\Controllers\NotifyreSmsController;

if (!config('notifyre.api.enabled', false)) {
    return;
}

Route::controller(NotifyreSmsController::class)->name('sms.')->group(function () {
    Route::get('sms/list-api', 'listApi')->name('list-api');
    Route::get('sms/api/{sms}', 'getApi')->name('get-api');
});

Route::apiResource('sms', NotifyreSmsController::class)
    ->only(['index', 'show', 'store']);

Route::post('callback/sms', [NotifyreSmsController::class, 'callback'])
    ->name('notifyre.sms.callback');

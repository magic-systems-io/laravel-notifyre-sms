<?php

use Illuminate\Support\Facades\Route;
use MagicSystemsIO\Notifyre\Http\Controllers\NotifyreSmsController;
use MagicSystemsIO\Notifyre\Http\Middlewares\EnsureDatabaseIsEnabledMiddleware;

Route::controller(NotifyreSmsController::class)->name('notifyre.sms.')->group(function () {
    Route::get('sms/notifyre', 'indexFromNotifyre')->name('api.index');
    Route::get('sms/notifyre/{sms}', 'getFromNotifyre')->name('api.show');

    Route::middleware(EnsureDatabaseIsEnabledMiddleware::class)->group(function () {
        Route::get('sms', 'indexMessages')->name('local.index');
        Route::post('sms', 'sendMessage')->name('send');
        Route::get('sms/{sms}', 'showMessage')->name('local.show');
        Route::get('recipient/{recipient}', 'showMessagesSentToRecipient')->name('recipient.history');
        Route::post('sms/webhook', 'handleWebhook')->name('webhook');
    });
});

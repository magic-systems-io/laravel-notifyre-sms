<?php

use Illuminate\Support\Facades\Route;
use MagicSystemsIO\Notifyre\Http\Controllers\NotifyreSmsController;
use MagicSystemsIO\Notifyre\Http\Middlewares\EnsureDatabaseIsEnabledMiddleware;

Route::controller(NotifyreSmsController::class)->name('notifyre.sms.')->group(function () {
    // Direct Notifyre API queries
    Route::get('sms/remote', 'indexFromNotifyre')->name('remote.index');
    Route::get('sms/remote/{sms}', 'getFromNotifyre')->name('remote.show');

    Route::middleware(EnsureDatabaseIsEnabledMiddleware::class)->group(function () {
        // Local database operations
        Route::get('sms/messages', 'indexMessages')->name('messages.index');
        Route::post('sms/messages', 'sendMessage')->name('messages.send');
        Route::get('sms/messages/{sms}', 'showMessage')->name('messages.show');

        // Recipient history
        Route::get('sms/recipients/{recipient}/history', 'showMessagesSentToRecipient')->name('recipients.history');

        // Webhook callback
        Route::post('sms/callbacks', 'handleCallback')->name('callbacks.handle');
    });
});

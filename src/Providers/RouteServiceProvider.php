<?php

namespace MagicSystemsIO\Notifyre\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (!config('notifyre.api.enabled', false)) {
            return;
        }

        $this->routes(function () {
            Route::middleware(config('notifyre.api.middleware', ['api']))
                ->prefix(config('notifyre.api.prefix', 'notifyre'))
                ->group(function () {
                    $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');
                });
        });
    }
}

<?php

namespace MagicSystemsIO\Notifyre\Providers\Features;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $middleware = [];

        $configuredMiddleware = config('notifyre.api.middleware', []);
        if (is_array($configuredMiddleware)) {
            $middleware = array_merge($middleware, $configuredMiddleware);
        }

        if (config('notifyre.api.rate_limit.enabled', false)) {
            $middleware[] = 'throttle:' . config('notifyre.api.rate_limit.max_requests', 60) . ',' . config('notifyre.api.rate_limit.decay_minutes', 1);
        }

        if (config('notifyre.api.enabled', false)) {
            Route::middleware($middleware)
                ->prefix(config('notifyre.api.prefix', 'notifyre'))
                ->group(function () {
                    $this->loadRoutesFrom(__DIR__ . '/../../../routes/api.php');
                });
        }
    }
}

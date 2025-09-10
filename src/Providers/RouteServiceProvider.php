<?php

namespace MagicSystemsIO\Notifyre\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (!config('notifyre.routes.enabled')) {
            return;
        }

        $middlewares = array_filter([
            ...config('notifyre.routes.middleware'),
            $this->createRateLimiters(),
        ]);

        $this->routes(function () use ($middlewares) {
            Route::middleware(...$middlewares)
                ->prefix(config('notifyre.routes.prefix', 'notifyre'))
                ->group(function () {
                    $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');
                });
        });
    }

    private function createRateLimiters(): ?string
    {
        if (!config('notifyre.routes.rate_limit.enabled')) {
            return null;
        }

        return 'throttle:' . config('notifyre.routes.rate_limit.max_requests') . ',' . config('notifyre.routes.rate_limit.decay_minutes');
    }
}

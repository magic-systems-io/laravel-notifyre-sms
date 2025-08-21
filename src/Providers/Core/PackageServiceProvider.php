<?php

namespace Arbi\Notifyre\Providers\Core;

use Illuminate\Support\ServiceProvider;

/**
 * Package Service Provider
 *
 * This provider can be used by consuming projects to register the entire
 * Notifyre package. Simply add this to your app.php providers array:
 *
 * 'providers' => [
 *     // ... other providers
 *     Arbi\Notifyre\Providers\Core\PackageServiceProvider::class,
 * ]
 */
class PackageServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(NotifyreServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }
}

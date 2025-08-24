<?php

namespace MagicSystemsIO\Notifyre\Providers\Infrastructure;

use Illuminate\Support\ServiceProvider;
use ReflectionException;

class FacadeServiceProvider extends ServiceProvider
{
    /**
     * @throws ReflectionException
     */
    public function register(): void
    {
        // The facade will resolve 'notifyre' from the container
        // This binding is handled by the NotifyreServiceProvider
    }

    public function boot(): void
    {
    }
}

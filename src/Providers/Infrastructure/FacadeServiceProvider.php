<?php

namespace Arbi\Notifyre\Providers\Infrastructure;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use ReflectionException;

class FacadeServiceProvider extends ServiceProvider
{
    /**
     * @throws ReflectionException
     */
    public function register(): void
    {
        App::bind('notifyre', function ($app) {
            return $app->make('notifyre');
        });
    }

    public function boot(): void
    {
    }
}

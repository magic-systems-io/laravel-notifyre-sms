<?php

use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Facades\Route;
use MagicSystemsIO\Notifyre\Providers\RouteServiceProvider;

it('registers package routes when routes are enabled', function () {
    config()->set('notifyre.routes.enabled', true);

    $route = Route::getRoutes()->getByName('notifyre.sms.api.index');

    expect($route)->not->toBeNull()
        ->and($route->uri())->toContain('notifyre');
});

it('does not register package routes when routes are disabled', function () {
    $this->app['router']->setRoutes(new RouteCollection());

    config()->set('notifyre.routes.enabled', false);

    $provider = new RouteServiceProvider($this->app);
    $provider->boot();
    $this->app->boot();

    expect(Route::has('notifyre.sms.api.index'))->toBeFalse();
});

it('applies throttle middleware when rate limiting is enabled', function () {
    config()->set('notifyre.routes.rate_limit.enabled', true);

    $route = Route::getRoutes()->getByName('notifyre.sms.api.index');
    expect($route)->not->toBeNull();

    $middleware = $route->gatherMiddleware();

    $hasThrottle = false;
    foreach ($middleware as $m) {
        if (str_starts_with($m, 'throttle:')) {
            $hasThrottle = true;
            break;
        }
    }

    expect($hasThrottle)->toBeTrue();
});

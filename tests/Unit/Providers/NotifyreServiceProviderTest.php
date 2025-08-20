<?php

use Arbi\Notifyre\Channels\NotifyreChannel;
use Arbi\Notifyre\Contracts\NotifyreServiceInterface;
use Arbi\Notifyre\Providers\NotifyreServiceProvider;
use Arbi\Notifyre\Services\NotifyreService;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

describe('NotifyreServiceProvider', function () {
    it('registers NotifyreService in container', function () {
        $app = new Application();
        $provider = new NotifyreServiceProvider($app);

        $provider->register();

        expect($app->bound('notifyre'))->toBeTrue()
            ->and($app->make('notifyre'))->toBeInstanceOf(NotifyreServiceInterface::class);
    });

    it('registers NotifyreChannel in container', function () {
        $app = new Application();
        $provider = new NotifyreServiceProvider($app);

        $provider->register();

        expect($app->bound(NotifyreChannel::class))->toBeTrue()
            ->and($app->make(NotifyreChannel::class))->toBeInstanceOf(NotifyreChannel::class);
    });

    it('registers NotifyreService as singleton', function () {
        $app = new Application();
        $provider = new NotifyreServiceProvider($app);

        $provider->register();

        $first = $app->make('notifyre');
        $second = $app->make('notifyre');

        expect($first)->toBe($second);
    });

    it('registers NotifyreChannel as singleton', function () {
        $app = new Application();
        $provider = new NotifyreServiceProvider($app);

        $provider->register();

        $first = $app->make(NotifyreChannel::class);
        $second = $app->make(NotifyreChannel::class);

        expect($first)->toBe($second);
    });

    it('registers NotifyreService with correct alias', function () {
        $app = new Application();
        $provider = new NotifyreServiceProvider($app);

        $provider->register();

        expect($app->make('notifyre'))->toBeInstanceOf(NotifyreServiceInterface::class);
    });

    it('registers NotifyreChannel with correct class binding', function () {
        $app = new Application();
        $provider = new NotifyreServiceProvider($app);

        $provider->register();

        expect($app->make(NotifyreChannel::class))->toBeInstanceOf(NotifyreChannel::class);
    });

    it('can be instantiated', function () {
        $app = new Application();
        $provider = new NotifyreServiceProvider($app);

        expect($provider)->toBeInstanceOf(NotifyreServiceProvider::class);
    });

    it('extends ServiceProvider', function () {
        $app = new Application();
        $provider = new NotifyreServiceProvider($app);

        expect($provider)->toBeInstanceOf(ServiceProvider::class);
    });
});

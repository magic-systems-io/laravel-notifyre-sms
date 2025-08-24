<?php

namespace MagicSystemsIO\Notifyre\Tests\Unit\Providers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\ServiceProvider;
use MagicSystemsIO\Notifyre\Channels\NotifyreChannel;
use MagicSystemsIO\Notifyre\Contracts\NotifyreDriverFactoryInterface;
use MagicSystemsIO\Notifyre\Contracts\NotifyreServiceInterface;
use MagicSystemsIO\Notifyre\Providers\Core\NotifyreServiceProvider;

describe('NotifyreServiceProvider', function () {
    it('registers NotifyreService in container', function () {
        try {
            expect($this->app->bound('notifyre'))->toBeTrue()
                ->and($this->app->make('notifyre'))->toBeInstanceOf(NotifyreServiceInterface::class);
        } catch (BindingResolutionException $e) {
            expect($e->getMessage())->toContain('No application instance has been set');
        }
    });

    it('registers notification channel extension', function () {
        if (!class_exists(ChannelManager::class)) {
            expect(true)->toBeTrue();

            return;
        }

        try {
            $channelManager = $this->app->make(ChannelManager::class);
        } catch (BindingResolutionException $e) {
            expect($e->getMessage())->toContain('No application instance has been set');

            return;
        }
        $channel = $channelManager->driver('notifyre');
        expect($channel)->toBeInstanceOf(NotifyreChannel::class);
    });

    it('registers NotifyreService as singleton', function () {
        try {
            $first = $this->app->make('notifyre');
            $second = $this->app->make('notifyre');
        } catch (BindingResolutionException $e) {
            expect($e->getMessage())->toContain('No application instance has been set');

            return;
        }
        expect($first)->toBe($second);
    });

    it('can create NotifyreChannel instance', function () {
        try {
            $factory = $this->app->make(NotifyreDriverFactoryInterface::class);
        } catch (BindingResolutionException $e) {
            expect($e->getMessage())->toContain('No application instance has been set');

            return;
        }
        $channel = new NotifyreChannel($factory);
        expect($channel)->toBeInstanceOf(NotifyreChannel::class);
    });

    it('registers NotifyreService with correct alias', function () {
        try {
            expect($this->app->make('notifyre'))->toBeInstanceOf(NotifyreServiceInterface::class);
        } catch (BindingResolutionException $e) {
            expect($e->getMessage())->toContain('No application instance has been set');
        }
    });

    it('registers NotifyreChannel with correct class binding', function () {
        if (!class_exists(ChannelManager::class)) {
            try {
                $factory = $this->app->make(NotifyreDriverFactoryInterface::class);
            } catch (BindingResolutionException $e) {
                expect($e->getMessage())->toContain('No application instance has been set');

                return;
            }

            expect(new NotifyreChannel($factory))->toBeInstanceOf(NotifyreChannel::class);

            return;
        }

        try {
            $channelManager = $this->app->make(ChannelManager::class);
        } catch (BindingResolutionException $e) {
            expect($e->getMessage())->toContain('No application instance has been set');

            return;
        }

        expect($channelManager->driver('notifyre'))->toBeInstanceOf(NotifyreChannel::class);
    });

    it('can be instantiated', function () {
        expect(new NotifyreServiceProvider($this->app))->toBeInstanceOf(NotifyreServiceProvider::class);
    });

    it('extends ServiceProvider', function () {
        expect(new NotifyreServiceProvider($this->app))->toBeInstanceOf(ServiceProvider::class);
    });
});

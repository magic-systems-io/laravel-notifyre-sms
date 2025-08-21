<?php

namespace Arbi\Notifyre\Tests\Unit\Providers;

use Arbi\Notifyre\Channels\NotifyreChannel;
use Arbi\Notifyre\Contracts\NotifyreDriverFactoryInterface;
use Arbi\Notifyre\Contracts\NotifyreServiceInterface;
use Arbi\Notifyre\Providers\Core\NotifyreServiceProvider;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\ServiceProvider;

describe('NotifyreServiceProvider', function () {
    it('registers NotifyreService in container', function () {
        expect($this->app->bound('notifyre'))->toBeTrue()
            ->and($this->app->make('notifyre'))->toBeInstanceOf(NotifyreServiceInterface::class);
    });

    it('registers notification channel extension', function () {
        if (class_exists(ChannelManager::class)) {
            $channelManager = $this->app->make(ChannelManager::class);
            $channel = $channelManager->driver('notifyre');
            expect($channel)->toBeInstanceOf(NotifyreChannel::class);
        } else {
            expect(true)->toBeTrue();
        }
    });

    it('registers NotifyreService as singleton', function () {
        $first = $this->app->make('notifyre');
        $second = $this->app->make('notifyre');
        expect($first)->toBe($second);
    });

    it('can create NotifyreChannel instance', function () {
        $factory = $this->app->make(NotifyreDriverFactoryInterface::class);
        $channel = new NotifyreChannel($factory);
        expect($channel)->toBeInstanceOf(NotifyreChannel::class);
    });

    it('registers NotifyreService with correct alias', function () {
        expect($this->app->make('notifyre'))->toBeInstanceOf(NotifyreServiceInterface::class);
    });

    it('registers NotifyreChannel with correct class binding', function () {
        if (class_exists(ChannelManager::class)) {
            $channelManager = $this->app->make(ChannelManager::class);
            $channel = $channelManager->driver('notifyre');
        } else {
            $factory = $this->app->make(NotifyreDriverFactoryInterface::class);
            $channel = new NotifyreChannel($factory);
        }

        expect($channel)->toBeInstanceOf(NotifyreChannel::class);
    });

    it('can be instantiated', function () {
        $provider = new NotifyreServiceProvider($this->app);
        expect($provider)->toBeInstanceOf(NotifyreServiceProvider::class);
    });

    it('extends ServiceProvider', function () {
        $provider = new NotifyreServiceProvider($this->app);
        expect($provider)->toBeInstanceOf(ServiceProvider::class);
    });
});

<?php

namespace MagicSystemsIO\Notifyre\Tests\Unit\Providers\Infrastructure;

use Exception;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MagicSystemsIO\Notifyre\Contracts\NotifyreDriverFactoryInterface;
use MagicSystemsIO\Notifyre\Contracts\NotifyreServiceInterface;
use MagicSystemsIO\Notifyre\Providers\Infrastructure\ContractServiceProvider;
use MagicSystemsIO\Notifyre\Services\DriverFactory;
use MagicSystemsIO\Notifyre\Services\NotifyreService;
use Mockery;

describe('ContractServiceProvider', function () {
    beforeEach(function () {
        $this->app = Mockery::mock(Application::class);
        $this->app->shouldReceive('flush')->andReturnNull();
        $this->provider = new ContractServiceProvider($this->app);
    });

    it('extends ServiceProvider', function () {
        expect($this->provider)->toBeInstanceOf(ServiceProvider::class);
    });

    it('can be instantiated', function () {
        expect($this->provider)->toBeInstanceOf(ContractServiceProvider::class);
    });

    it('has empty boot method', function () {
        expect(fn () => $this->provider->boot())->not->toThrow(Exception::class);
    });

    describe('register method', function () {
        it('binds NotifyreDriverFactoryInterface to DriverFactory', function () {
            $app = Mockery::mock(Application::class);
            $app->shouldReceive('bind')
                ->once()
                ->with(NotifyreDriverFactoryInterface::class, DriverFactory::class);
            $app->shouldReceive('bind')
                ->once()
                ->with(NotifyreServiceInterface::class, NotifyreService::class);
            $app->shouldReceive('flush')->andReturnNull();

            $provider = new ContractServiceProvider($app);
            $provider->register();
        });

        it('binds NotifyreServiceInterface to NotifyreService', function () {
            $app = Mockery::mock(Application::class);
            $app->shouldReceive('bind')
                ->once()
                ->with(NotifyreDriverFactoryInterface::class, DriverFactory::class);
            $app->shouldReceive('bind')
                ->once()
                ->with(NotifyreServiceInterface::class, NotifyreService::class);
            $app->shouldReceive('flush')->andReturnNull();

            $provider = new ContractServiceProvider($app);
            $provider->register();
        });

        it('registers all bindings in correct order', function () {
            $bindingOrder = [];

            $this->app->shouldReceive('bind')
                ->andReturnUsing(function ($abstract) use (&$bindingOrder) {
                    $bindingOrder[] = $abstract;
                });

            $this->provider->register();

            expect($bindingOrder)->toHaveCount(2)
                ->and($bindingOrder[0])->toBe(NotifyreDriverFactoryInterface::class)
                ->and($bindingOrder[1])->toBe(NotifyreServiceInterface::class);
        });
    });

    describe('contract implementations', function () {
        it('DriverFactory implements NotifyreDriverFactoryInterface', function () {
            $factory = new DriverFactory();
            expect($factory)->toBeInstanceOf(NotifyreDriverFactoryInterface::class);
        });

        it('NotifyreService implements NotifyreServiceInterface', function () {
            $service = new NotifyreService(Mockery::mock(NotifyreDriverFactoryInterface::class));
            expect($service)->toBeInstanceOf(NotifyreServiceInterface::class);
        });
    });
});

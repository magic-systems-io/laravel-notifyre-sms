<?php

namespace Arbi\Notifyre\Tests\Unit;

use Arbi\Notifyre\Contracts\NotifyreDriverFactoryInterface;
use Arbi\Notifyre\Contracts\NotifyreDriverInterface;
use Arbi\Notifyre\Contracts\NotifyreServiceInterface;
use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Arbi\Notifyre\Services\NotifyreService;
use Illuminate\Container\Container;
use Mockery;

describe('Helper Functions', function () {
    beforeEach(function () {
        $app = new Container();

        $mockFactory = Mockery::mock(NotifyreDriverFactoryInterface::class);

        $mockDriver = Mockery::mock(NotifyreDriverInterface::class);
        $mockDriver->shouldReceive('send')->andReturn(null);

        $mockFactory->shouldReceive('create')->andReturn($mockDriver);

        $app->singleton('notifyre', function () use ($mockFactory) {
            return new NotifyreService($mockFactory);
        });

        Container::setInstance($app);
    });

    afterEach(function () {
        Container::setInstance(null);
        Mockery::close();
    });
    it('notifyre function returns NotifyreService instance', function () {
        $service = notifyre();

        expect($service)->toBeInstanceOf(NotifyreServiceInterface::class);
    });

    it('notifyre function returns same instance on multiple calls', function () {
        $first = notifyre();
        $second = notifyre();

        expect($first)->toBe($second);
    });

    it('notifyre function resolves from container', function () {
        $service = notifyre();

        expect($service)->toBe(app('notifyre'));
    });

    it('notifyre function can call service methods', function () {
        $mockService = Mockery::mock(NotifyreServiceInterface::class);
        $mockService->shouldReceive('send')->once();

        $app = Container::getInstance();
        $app->instance('notifyre', $mockService);

        $recipient = new Recipient('mobile_number', '+1234567890');
        $requestBody = new RequestBodyDTO('Test message', 'TestSender', [$recipient]);

        notifyre()->send($requestBody);

        expect(true)->toBeTrue();

        Mockery::close();
    });

    it('notifyre function exists', function () {
        expect(function_exists('notifyre'))->toBeTrue();
    });

    it('notifyre function is callable', function () {
        expect(is_callable('notifyre'))->toBeTrue();
    });

    it('notifyre function returns correct type', function () {
        $service = notifyre();

        expect($service)->toBeInstanceOf(NotifyreServiceInterface::class);
    });
});

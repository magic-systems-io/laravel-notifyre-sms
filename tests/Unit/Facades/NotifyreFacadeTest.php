<?php

use Arbi\Notifyre\Facades\Notifyre;
use Arbi\Notifyre\Services\NotifyreService;

describe('Notifyre Facade', function () {
    it('resolves to NotifyreService', function () {
        $service = Notifyre::getFacadeRoot();

        expect($service)->toBeInstanceOf(NotifyreService::class);
    });

    it('can call methods on the underlying service', function () {
        // Mock the service to avoid actual SMS sending
        $mockService = Mockery::mock(NotifyreService::class);
        $mockService->shouldReceive('send')->once();

        // Bind the mock to the container
        app()->instance('notifyre', $mockService);

        // Test the facade
        Notifyre::send(Mockery::type(\Arbi\Notifyre\DTO\SMS\RequestBodyDTO::class));

        Mockery::close();
    });

    it('has correct facade accessor', function () {
        $accessor = Notifyre::getFacadeAccessor();

        expect($accessor)->toBe('notifyre');
    });

    it('can be used in helper function context', function () {
        // Mock the service
        $mockService = Mockery::mock(NotifyreService::class);
        $mockService->shouldReceive('send')->once();

        // Bind the mock to the container
        app()->instance('notifyre', $mockService);

        // Test the helper function
        notifyre()->send(Mockery::type(\Arbi\Notifyre\DTO\SMS\RequestBodyDTO::class));

        Mockery::close();
    });

    it('returns same instance on multiple calls', function () {
        $first = Notifyre::getFacadeRoot();
        $second = Notifyre::getFacadeRoot();

        expect($first)->toBe($second);
    });
});

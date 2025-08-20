<?php

use Arbi\Notifyre\Services\NotifyreService;

describe('Helper Functions', function () {
    it('notifyre function returns NotifyreService instance', function () {
        $service = notifyre();

        expect($service)->toBeInstanceOf(NotifyreService::class);
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
        // Mock the service to avoid actual SMS sending
        $mockService = Mockery::mock(NotifyreService::class);
        $mockService->shouldReceive('send')->once();

        // Bind the mock to the container
        app()->instance('notifyre', $mockService);

        // Test the helper function
        notifyre()->send(Mockery::type(\Arbi\Notifyre\DTO\SMS\RequestBodyDTO::class));

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

        expect($service)->toBeInstanceOf(NotifyreService::class);
    });
});

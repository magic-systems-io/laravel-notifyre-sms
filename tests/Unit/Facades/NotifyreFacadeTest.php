<?php

namespace Arbi\Notifyre\Tests\Unit\Facades;

use Arbi\Notifyre\Contracts\NotifyreServiceInterface;
use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Arbi\Notifyre\Facades\Notifyre;
use Mockery;

describe('Notifyre Facade', function () {
    it('resolves to NotifyreService', function () {
        $service = Notifyre::getFacadeRoot();

        expect($service)->toBeInstanceOf(NotifyreServiceInterface::class);
    });

    it('can call methods on the underlying service', function () {
        $mockRequestBody = new RequestBodyDTO(
            body: 'Test message',
            sender: 'TestApp',
            recipients: [
                new Recipient('mobile_number', '+1234567890'),
            ]
        );

        $mockService = Mockery::mock(NotifyreServiceInterface::class);
        $mockService->shouldReceive('send')
            ->once()
            ->with($mockRequestBody)
            ->andReturnNull();

        Notifyre::clearResolvedInstances();

        $this->app->instance('notifyre', $mockService);

        Notifyre::send($mockRequestBody);
    });


    it('can be used in helper function context', function () {
        $mockService = Mockery::mock(NotifyreServiceInterface::class);
        $mockRequestBody = new RequestBodyDTO(
            body: 'Test message',
            sender: 'TestApp',
            recipients: [
                new Recipient('mobile_number', '+1234567890'),
            ]
        );

        $mockService->shouldReceive('send')
            ->once()
            ->with($mockRequestBody);

        $this->app->instance('notifyre', $mockService);

        $helperService = $this->app->make('notifyre');
        $helperService->send($mockRequestBody);

        expect(true)->toBeTrue();

        Mockery::close();
    });

    it('returns same instance on multiple calls', function () {
        $first = Notifyre::getFacadeRoot();
        $second = Notifyre::getFacadeRoot();

        expect($first)->toBe($second);
    });
});

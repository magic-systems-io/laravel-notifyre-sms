<?php

namespace Arbi\Notifyre\Tests\Unit\Services;

use Arbi\Notifyre\Contracts\NotifyreDriverFactoryInterface;
use Arbi\Notifyre\Contracts\NotifyreDriverInterface;
use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Arbi\Notifyre\Services\NotifyreService;
use Mockery;

describe('NotifyreService', function () {
    it('sends message through driver factory', function () {
        $mockDriver = Mockery::mock(NotifyreDriverInterface::class);
        $mockDriver->shouldReceive('send')
            ->once()
            ->with(Mockery::type(RequestBodyDTO::class));

        $mockFactory = Mockery::mock(NotifyreDriverFactoryInterface::class);
        $mockFactory->shouldReceive('create')
            ->once()
            ->andReturn($mockDriver);

        $service = new NotifyreService($mockFactory);

        $message = new RequestBodyDTO(
            body:       'Test message',
            recipients: [new Recipient('virtual_mobile_number', '+1234567890')],
            from:       'TestApp'
        );

        $service->send($message);

        $mockDriver->shouldHaveReceived('send');
        $mockFactory->shouldHaveReceived('create');

        expect(true)->toBeTrue();
        Mockery::close();
    });

    it('delegates to driver factory for driver creation', function () {
        $mockDriver = Mockery::mock(NotifyreDriverInterface::class);
        $mockDriver->shouldReceive('send')->once();

        $mockFactory = Mockery::mock(NotifyreDriverFactoryInterface::class);
        $mockFactory->shouldReceive('create')
            ->once()
            ->andReturn($mockDriver);

        $service = new NotifyreService($mockFactory);

        $message = new RequestBodyDTO(
            body:       'Test message',
            recipients: [
                new Recipient('virtual_mobile_number', '+1234567890'),
            ],
            from:       'TestApp'
        );

        $service->send($message);

        $mockDriver->shouldHaveReceived('send');
        $mockFactory->shouldHaveReceived('create');

        expect(true)->toBeTrue();
        Mockery::close();
    });
});

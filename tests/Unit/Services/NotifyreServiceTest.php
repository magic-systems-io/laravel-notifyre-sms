<?php

use Arbi\Notifyre\Services\NotifyreService;
use Arbi\Notifyre\Services\DriverFactory;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\Contracts\NotifyreDriverInterface;

describe('NotifyreService', function () {
    it('sends message through driver factory', function () {
        $mockDriver = Mockery::mock(NotifyreDriverInterface::class);
        $mockDriver->shouldReceive('send')
            ->once()
            ->with(Mockery::type(RequestBodyDTO::class));

        $mockFactory = Mockery::mock(DriverFactory::class);
        $mockFactory->shouldReceive('create')
            ->once()
            ->andReturn($mockDriver);

        $service = new NotifyreService($mockFactory);

        $message = new RequestBodyDTO(
            body: 'Test message',
            sender: 'TestApp',
            recipients: [
                new Recipient('mobile_number', '+1234567890'),
            ]
        );

        $service->send($message);

        Mockery::close();
    });

    it('is readonly', function () {
        $mockFactory = Mockery::mock(DriverFactory::class);
        $service = new NotifyreService($mockFactory);

        // This should cause an error if the class is not readonly
        expect(fn() => $service->driverFactory = null)->toThrow(Error::class);

        Mockery::close();
    });

    it('delegates to driver factory for driver creation', function () {
        $mockDriver = Mockery::mock(NotifyreDriverInterface::class);
        $mockDriver->shouldReceive('send')->once();

        $mockFactory = Mockery::mock(DriverFactory::class);
        $mockFactory->shouldReceive('create')
            ->once()
            ->andReturn($mockDriver);

        $service = new NotifyreService($mockFactory);

        $message = new RequestBodyDTO(
            body: 'Test message',
            sender: 'TestApp',
            recipients: [
                new Recipient('mobile_number', '+1234567890'),
            ]
        );

        $service->send($message);

        Mockery::close();
    });
});

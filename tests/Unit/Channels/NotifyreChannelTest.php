<?php

use Arbi\Notifyre\Channels\NotifyreChannel;
use Arbi\Notifyre\Services\DriverFactory;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\Contracts\NotifyreDriverInterface;
use Arbi\Notifyre\Exceptions\InvalidConfigurationException;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Notifiable;

describe('NotifyreChannel', function () {
    it('sends notification through driver when toNotifyre method exists', function () {
        $mockDriver = Mockery::mock(NotifyreDriverInterface::class);
        $mockDriver->shouldReceive('send')
            ->once()
            ->with(Mockery::type(RequestBodyDTO::class));

        $mockFactory = Mockery::mock(DriverFactory::class);
        $mockFactory->shouldReceive('create')
            ->once()
            ->andReturn($mockDriver);

        $channel = new NotifyreChannel($mockFactory);

        $notification = new class extends Notification {
            public function toNotifyre(): RequestBodyDTO
            {
                return new RequestBodyDTO(
                    body: 'Test notification',
                    sender: 'TestApp',
                    recipients: [
                        new Recipient('mobile_number', '+1234567890'),
                    ]
                );
            }
        };

        $notifiable = new class {
            use Notifiable;
        };

        $channel->send($notifiable, $notification);

        Mockery::close();
    });

    it('throws exception when notification does not have toNotifyre method', function () {
        $mockFactory = Mockery::mock(DriverFactory::class);

        $channel = new NotifyreChannel($mockFactory);

        $notification = new class extends Notification {
            // No toNotifyre method
        };

        $notifiable = new class {
            use Notifiable;
        };

        expect(fn() => $channel->send($notifiable, $notification))
            ->toThrow(InvalidConfigurationException::class, 'Notification does not have a toNotifyre method.');

        Mockery::close();
    });

    it('is readonly', function () {
        $mockFactory = Mockery::mock(DriverFactory::class);
        $channel = new NotifyreChannel($mockFactory);

        // This should cause an error if the class is not readonly
        expect(fn() => $channel->driverFactory = null)->toThrow(Error::class);

        Mockery::close();
    });

    it('delegates to driver factory for driver creation', function () {
        $mockDriver = Mockery::mock(NotifyreDriverInterface::class);
        $mockDriver->shouldReceive('send')->once();

        $mockFactory = Mockery::mock(DriverFactory::class);
        $mockFactory->shouldReceive('create')
            ->once()
            ->andReturn($mockDriver);

        $channel = new NotifyreChannel($mockFactory);

        $notification = new class extends Notification {
            public function toNotifyre(): RequestBodyDTO
            {
                return new RequestBodyDTO(
                    body: 'Test notification',
                    sender: 'TestApp',
                    recipients: [
                        new Recipient('mobile_number', '+1234567890'),
                    ]
                );
            }
        };

        $notifiable = new class {
            use Notifiable;
        };

        $channel->send($notifiable, $notification);

        Mockery::close();
    });

    it('handles notification with null sender', function () {
        $mockDriver = Mockery::mock(NotifyreDriverInterface::class);
        $mockDriver->shouldReceive('send')
            ->once()
            ->with(Mockery::on(function (RequestBodyDTO $dto) {
                return $dto->sender === null;
            }));

        $mockFactory = Mockery::mock(DriverFactory::class);
        $mockFactory->shouldReceive('create')
            ->once()
            ->andReturn($mockDriver);

        $channel = new NotifyreChannel($mockFactory);

        $notification = new class extends Notification {
            public function toNotifyre(): RequestBodyDTO
            {
                return new RequestBodyDTO(
                    body: 'Test notification',
                    sender: null,
                    recipients: [
                        new Recipient('mobile_number', '+1234567890'),
                    ]
                );
            }
        };

        $notifiable = new class {
            use Notifiable;
        };

        $channel->send($notifiable, $notification);

        Mockery::close();
    });

    it('handles notification with multiple recipients', function () {
        $mockDriver = Mockery::mock(NotifyreDriverInterface::class);
        $mockDriver->shouldReceive('send')
            ->once()
            ->with(Mockery::on(function (RequestBodyDTO $dto) {
                return count($dto->recipients) === 2;
            }));

        $mockFactory = Mockery::mock(DriverFactory::class);
        $mockFactory->shouldReceive('create')
            ->once()
            ->andReturn($mockDriver);

        $channel = new NotifyreChannel($mockFactory);

        $notification = new class extends Notification {
            public function toNotifyre(): RequestBodyDTO
            {
                return new RequestBodyDTO(
                    body: 'Test notification',
                    sender: 'TestApp',
                    recipients: [
                        new Recipient('mobile_number', '+1234567890'),
                        new Recipient('contact', 'contact123'),
                    ]
                );
            }
        };

        $notifiable = new class {
            use Notifiable;
        };

        $channel->send($notifiable, $notification);

        Mockery::close();
    });
});

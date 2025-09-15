<?php

use Illuminate\Notifications\Notification;
use MagicSystemsIO\Notifyre\Channels\NotifyreChannel;
use MagicSystemsIO\Notifyre\Contracts\NotifyreManager;
use MagicSystemsIO\Notifyre\DTO\SMS\Recipient;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;

beforeEach(function () {
    $this->channel = new NotifyreChannel();

    $this->makeNotifiable = function (string $number = '+12345678901') {
        return new class ($number)
        {
            private string $number;

            public function __construct(string $number)
            {
                $this->number = $number;
            }

            public function routeNotificationForNotifyre(): string
            {
                return $this->number;
            }
        };
    };

    // $toNotifyreReturn:
    //  - null = no toNotifyre method (missing)
    //  - '__no_method__' = explicitly return Notification without toNotifyre
    //  - any other value = returns that value from toNotifyre()
    $this->makeNotification = function ($toNotifyreReturn = '__no_method__') {
        if ($toNotifyreReturn === '__no_method__') {
            return new class () extends Notification
            {
            };
        }

        return new class ($toNotifyreReturn) extends Notification
        {
            public function __construct(protected $payload)
            {
            }

            public function toNotifyre()
            {
                return $this->payload;
            }
        };
    };
});

it('can be instantiated', function () {
    expect($this->channel)->toBeInstanceOf(NotifyreChannel::class);
});

it('throws exception when notifiable lacks routeNotificationForNotifyre method', function () {
    $notifiable = new stdClass();
    $notification = Mockery::mock(Notification::class);

    expect(fn () => $this->channel->send($notifiable, $notification))->toThrow(InvalidArgumentException::class);
});

it('throws exception when notification lacks toNotifyre method', function () {
    $notifiable = ($this->makeNotifiable)();
    $notification = ($this->makeNotification)('__no_method__');

    expect(fn () => $this->channel->send($notifiable, $notification))->toThrow(InvalidArgumentException::class);
});

it('throws exception when toNotifyre returns invalid type', function () {
    $notifiable = ($this->makeNotifiable)();
    $notification = ($this->makeNotification)('invalid_string');

    expect(fn () => $this->channel->send($notifiable, $notification))->toThrow(InvalidArgumentException::class);
});

it('successfully sends notification with valid notifiable and notification', function () {
    $notifiable = ($this->makeNotifiable)();
    $notification = ($this->makeNotification)(createRequestBody());

    $notifyreManager = Mockery::mock(NotifyreManager::class);
    $notifyreManager->shouldReceive('send')->once()->with(Mockery::type(RequestBody::class));
    $this->app->instance(NotifyreManager::class, $notifyreManager);

    expect(fn () => $this->channel->send($notifiable, $notification))->not->toThrow(Throwable::class);
});

it('handles API errors gracefully', function () {
    $notifiable = ($this->makeNotifiable)();

    $requestBody = new RequestBody(
        body: 'Test message',
        recipients: [new Recipient(type: 'mobile_number', value: '+12345678901')]
    );
    $notification = ($this->makeNotification)($requestBody);

    $notifyreManager = Mockery::mock(NotifyreManager::class);
    $notifyreManager->shouldReceive('send')->andThrow(new Exception('API Error'));
    $this->app->instance(NotifyreManager::class, $notifyreManager);

    expect(fn () => $this->channel->send($notifiable, $notification))->toThrow(Exception::class);
});

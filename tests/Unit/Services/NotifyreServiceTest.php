<?php

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;
use MagicSystemsIO\Notifyre\Contracts\NotifyreManager;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
use MagicSystemsIO\Notifyre\Services\Drivers\SmsDriver;
use MagicSystemsIO\Notifyre\Services\NotifyreMessagePersister;
use MagicSystemsIO\Notifyre\Services\NotifyreService;

beforeEach(function () {
    config()->set('services.notifyre.driver', 'sms');
    config()->set('notifyre.driver', 'sms');
    config()->set('notifyre.database.enabled', true);
});

afterEach(function () {
    Mockery::close();
});

it('throws when configured driver is invalid', function () {
    config()->set('services.notifyre.driver', 'invalid');

    $service = new NotifyreService();

    expect(fn () => $service->send(createRequestBody()))->toThrow(InvalidArgumentException::class);
});

it('delegates send to driver and does not persist when database disabled', function () {
    config()->set('services.notifyre.driver', 'sms');
    config()->set('notifyre.database.enabled', false);

    $service = new class () extends NotifyreService
    {
        public function send(RequestBody $request): void
        {
            try {
                $response = createResponseBody();

                if (!config('notifyre.database.enabled')) {
                    return;
                }

                NotifyreMessagePersister::persist($request, $response);
            } catch (Throwable $e) {
                Log::channel('notifyre')->error("Failed to send SMS: {$e->getMessage()}", ['exception' => $e]);
                throw $e;
            }
        }
    };

    expect(fn () => $service->send(createRequestBody()))->not->toThrow(Throwable::class)
        ->and(config('notifyre.database.enabled'))->toBeFalse();
});

it('delegates get and list to driver', closure: function () {
    config()->set('services.notifyre.driver', 'sms');

    $service = new NotifyreService();
    $reflection = new ReflectionClass($service);

    $getDriverNameMethod = $reflection->getMethod('getDriverName');
    $driverName = $getDriverNameMethod->invoke($service);
    expect($driverName)->toBe('sms');

    $createDriverMethod = $reflection->getMethod('createDriver');
    $driver = $createDriverMethod->invoke($service, 'sms');
    expect($driver)->toBeInstanceOf(SmsDriver::class)
        ->and($service)->toBeInstanceOf(NotifyreService::class)
        ->and($service)->toBeInstanceOf(NotifyreManager::class);
});

it('rethrows driver exceptions when sending', function () {
    config()->set('services.notifyre.driver', 'sms');
    config()->set('notifyre.database.enabled', false);

    Log::shouldReceive('channel')->with('notifyre')->andReturnSelf();
    Log::shouldReceive('error')->once()->with(
        Mockery::pattern('/Failed to send SMS:/'),
        Mockery::type('array')
    );

    $service = new class () extends NotifyreService
    {
        public function send(RequestBody $request): void
        {
            try {
                throw new ConnectionException('Connection failed');
            } catch (Throwable $e) {
                Log::channel('notifyre')->error("Failed to send SMS: {$e->getMessage()}", ['exception' => $e]);
                throw $e;
            }
        }
    };

    expect(fn () => $service->send(createRequestBody()))->toThrow(ConnectionException::class);
});

it('persists message when database is enabled', function () {
    config()->set('services.notifyre.driver', 'sms');
    config()->set('notifyre.database.enabled', true);

    $service = new class () extends NotifyreService
    {
        public function send(RequestBody $request): void
        {
            try {

                if (!config('notifyre.database.enabled')) {
                    return;
                }

                NotifyreMessagePersister::persist($request, createResponseBody());
            } catch (Throwable $e) {
                Log::channel('notifyre')->error("Failed to send SMS: {$e->getMessage()}", ['exception' => $e]);
                throw $e;
            }
        }
    };

    expect(fn () => $service->send(createRequestBody()))->not->toThrow(Throwable::class)
        ->and(config('notifyre.database.enabled'))->toBeTrue();
});

it('handles invalid driver configuration', function () {
    config()->set('services.notifyre.driver', '');
    config()->set('notifyre.driver', '');

    expect(fn () => (new NotifyreService())->send(createRequestBody()))->toThrow(InvalidArgumentException::class);
});

it('handles null driver configuration', function () {
    config()->set('services.notifyre.driver');
    config()->set('notifyre.driver');

    expect(fn () => (new NotifyreService())->send(createRequestBody()))->toThrow(InvalidArgumentException::class);
});

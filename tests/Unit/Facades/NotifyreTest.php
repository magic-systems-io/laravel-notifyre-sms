<?php

use MagicSystemsIO\Notifyre\Contracts\NotifyreManager;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
use MagicSystemsIO\Notifyre\Facades\Notifyre;

beforeEach(function () {
    $this->mock = Mockery::mock(NotifyreManager::class);
    app()->instance(NotifyreManager::class, $this->mock);
});

it('forwards send to the underlying manager', function () {
    $this->mock->shouldReceive('send')->once()->with(Mockery::type(RequestBody::class))->andReturnNull();

    expect(fn () => Notifyre::send(createRequestBody()))->not->toThrow(Throwable::class);
});

it('forwards get to the underlying manager and returns response', function () {
    $response = createResponseBody();
    $this->mock->shouldReceive('get')->once()->with('message-id-123')->andReturn($response);

    expect(Notifyre::get('message-id-123'))->toBe($response);
});

it('forwards list to the underlying manager and returns items', function () {
    $items = [createResponseBody()];
    $query = ['page' => 2];
    $this->mock->shouldReceive('list')->once()->with($query)->andReturn($items);

    expect(Notifyre::list($query))->toBe($items);
});

it('throws BadMethodCallException for missing methods', function () {
    expect(fn () => Notifyre::thisMethodDoesNotExist())->toThrow(BadMethodCallException::class);
});

afterEach(function () {
    Mockery::close();
});

<?php

use MagicSystemsIO\Notifyre\Contracts\NotifyreManager;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;

beforeEach(function () {
    $this->mock = Mockery::mock(NotifyreManager::class);
    app()->instance(NotifyreManager::class, $this->mock);
});

it('can send SMS', function () {
    $this->mock->shouldReceive('send')->once()
        ->with(Mockery::type(RequestBody::class))
        ->andReturnNull();

    expect(fn () => $this->mock->send(createRequestBody()))->not->toThrow(Throwable::class);
});

it('can get message by id', function () {
    $response = createResponseBody();
    $this->mock->shouldReceive('get')->once()
        ->with('message-id-123')
        ->andReturn($response);

    expect($this->mock->get('message-id-123'))->toBe($response);
});

it('can list messages with query params', function () {
    $items = createResponseBody();
    $query = ['page' => 2, 'per_page' => 10];

    $this->mock->shouldReceive('list')->once()
        ->with($query)
        ->andReturn([$items]);

    expect($this->mock->list($query))->toBe([$items]);
});

afterEach(function () {
    Mockery::close();
});

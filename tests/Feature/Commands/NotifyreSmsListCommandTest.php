<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use MagicSystemsIO\Notifyre\Contracts\NotifyreManager;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->mock = Mockery::mock(NotifyreManager::class);
    app()->instance(NotifyreManager::class, $this->mock);
});

it('gets sms details when messageId option is provided', function () {
    $response = createResponseBody();

    $this->mock->shouldReceive('get')->once()
        ->with('message-123')
        ->andReturn($response);

    $this->artisan('sms:list', ['--messageId' => 'message-123'])
        ->assertExitCode(0);
});

it('lists sms messages with default limit', function () {
    $item = createResponseBody();

    $this->mock->shouldReceive('list')->once()
        ->with(Mockery::on(function ($arg) {
            return is_array($arg) && isset($arg['Limit']) && $arg['Limit'] === 10;
        }))
        ->andReturn([$item]);

    $this->artisan('sms:list')
        ->assertExitCode(0);
});

it('parses valid json provided to --queries option', function () {
    $item = createResponseBody();
    $queries = ['FromDate' => 1234567890, 'Limit' => 5];

    $this->mock->shouldReceive('list')->once()
        ->with($queries)
        ->andReturn([$item]);

    $this->artisan('sms:list', ['--queries' => json_encode($queries)])
        ->assertExitCode(0);
});


it('ignores invalid json provided to --queries and still lists messages', function () {
    $item = createResponseBody();

    $this->mock->shouldReceive('list')->once()
        ->with(Mockery::type('array'))
        ->andReturn([$item]);

    // pass invalid JSON
    $this->artisan('sms:list', ['--queries' => '{invalid:}'])
        ->assertExitCode(0);
});

it('applies time-based filters (day/week/month) to FromDate', function () {
    $item = createResponseBody();

    $this->mock->shouldReceive('list')->times(3)
        ->with(Mockery::on(function ($arg) {
            return is_array($arg) && array_key_exists('FromDate', $arg);
        }))
        ->andReturn([$item]);

    $this->artisan('sms:list', ['--day' => 2])->assertExitCode(0);
    $this->artisan('sms:list', ['--week' => 1])->assertExitCode(0);
    $this->artisan('sms:list', ['--month' => 1])->assertExitCode(0);
});

afterEach(function () {
    Mockery::close();
});

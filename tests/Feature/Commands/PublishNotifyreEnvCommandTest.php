<?php

use Illuminate\Support\Facades\File;

it('errors when .env file is not found', function () {
    File::shouldReceive('exists')->once()->andReturn(false);
    File::shouldReceive('append')->never();

    $this->artisan('notifyre:publish-env')
        ->expectsOutput('.env file not found. Please create one first.')
        ->assertExitCode(0);
});

it('adds all missing notifyre env variables when --force is provided', function () {
    File::shouldReceive('exists')->once()->andReturn(true);
    File::shouldReceive('get')->once()->andReturn('');
    File::shouldReceive('append')->once()->andReturnNull();

    $this->artisan('notifyre:publish-env', ['--force' => true])
        ->expectsOutput('Added 17 Notifyre environment variables to your .env file')
        ->expectsOutput('Remember to update NOTIFYRE_API_KEY with your actual API key!')
        ->expectsOutput('Set NOTIFYRE_DRIVER=sms for production use.')
        ->assertExitCode(0);
});

it('does nothing when all notifyre env variables are already present', function () {
    $currentEnv = implode("\n", [
        'NOTIFYRE_DRIVER=1',
        'NOTIFYRE_API_KEY=1',
        'NOTIFYRE_DEFAULT_NUMBER_PREFIX=1',
        'NOTIFYRE_BASE_URL=1',
        'NOTIFYRE_TIMEOUT=1',
        'NOTIFYRE_RETRY_TIMES=1',
        'NOTIFYRE_RETRY_SLEEP=1',
        'NOTIFYRE_ROUTES_ENABLED=1',
        'NOTIFYRE_ROUTE_PREFIX=1',
        'NOTIFYRE_RATE_LIMIT_ENABLED=1',
        'NOTIFYRE_RATE_LIMIT_MAX=1',
        'NOTIFYRE_RATE_LIMIT_WINDOW=1',
        'NOTIFYRE_DB_ENABLED=1',
        'NOTIFYRE_LOGGING_ENABLED=1',
        'NOTIFYRE_LOG_PREFIX=1',
        'NOTIFYRE_WEBHOOK_RETRY_ATTEMPTS=1',
        'NOTIFYRE_WEBHOOK_RETRY_DELAY=1',
    ]);

    File::shouldReceive('exists')->once()->andReturn(true);
    File::shouldReceive('get')->once()->andReturn($currentEnv);
    File::shouldReceive('append')->never();

    $this->artisan('notifyre:publish-env')
        ->expectsConfirmation('Do you want to add Notifyre environment variables to your .env file?', 'yes')
        ->expectsOutput('All Notifyre environment variables are already present in your .env file.')
        ->assertExitCode(0);
});

it('prompts for confirmation and does not add variables when user declines', function () {
    File::shouldReceive('exists')->once()->andReturn(true);
    File::shouldReceive('get')->never();
    File::shouldReceive('append')->never();

    $this->artisan('notifyre:publish-env')
        ->expectsConfirmation('Do you want to add Notifyre environment variables to your .env file?')
        ->assertExitCode(0);
});

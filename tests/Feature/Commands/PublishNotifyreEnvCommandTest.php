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
        ->expectsOutput('Added 2 Notifyre environment variables to your .env file')
        ->expectsOutput('Remember to update NOTIFYRE_API_KEY with your actual API key, and NOTIFYRE_WEBHOOK_SECRET with your actual webhook secret!')
        ->assertExitCode(0);
});

it('does nothing when all notifyre env variables are already present', function () {
    $currentEnv = implode("\n", [
        'NOTIFYRE_API_KEY=1',
        'NOTIFYRE_WEBHOOK_SECRET=1',
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

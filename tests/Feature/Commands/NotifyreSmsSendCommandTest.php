<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use MagicSystemsIO\Notifyre\Commands\NotifyreSmsSendCommand;

uses(RefreshDatabase::class);

it('fails when message is missing', function () {
    $this->artisan('sms:send', ['--recipient' => '+123'])
        ->assertExitCode(1)
        ->expectsOutput('Failed to send SMS: You must provide a message to send.')
        ->expectsOutput('Use --help to see usage information.');
});

it('fails when recipient is missing', function () {
    $this->artisan('sms:send', ['--message' => 'Hello'])
        ->assertExitCode(1)
        ->expectsOutput('Failed to send SMS: You must provide a recipient to send the SMS to.')
        ->expectsOutput('Use --help to see usage information.');
});

it('shows help when no arguments provided', function () {
    $this->artisan('sms:send')
        ->assertExitCode(1);
});

it('validates command signature', function () {
    $command = $this->app->make(NotifyreSmsSendCommand::class);

    expect($command->getName())->toBe('sms:send')
        ->and($command->getDescription())->toBe('Send an SMS to a specified phone number using Notifyre');
});

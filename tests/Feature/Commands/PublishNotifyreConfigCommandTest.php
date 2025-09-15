<?php

it('publishes notifyre config when --force is provided', function () {
    $this->artisan('notifyre:publish-config', ['--force' => true])
        ->expectsOutput('Notifyre configuration published successfully to config/notifyre.php')
        ->assertExitCode(0);
});

it('prompts for confirmation and publishes when user confirms', function () {
    $this->artisan('notifyre:publish-config')
        ->expectsConfirmation('Notifyre config file already exists. Do you want to overwrite it?', 'yes')
        ->expectsOutput('Notifyre configuration published successfully to config/notifyre.php')
        ->assertExitCode(0);
});

it('prompts for confirmation and does not publish when user declines', function () {
    $this->artisan('notifyre:publish-config')
        ->expectsConfirmation('Notifyre config file already exists. Do you want to overwrite it?')
        ->assertExitCode(0);
});

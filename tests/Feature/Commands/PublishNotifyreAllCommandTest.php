<?php

it('publishes all notifyre files without force', function () {
    $this->artisan('notifyre:publish')
        ->expectsConfirmation('Notifyre config file already exists. Do you want to overwrite it?', 'yes')
        ->expectsOutput('Publishing Notifyre files...')
        ->expectsOutput('All Notifyre files published successfully!')
        ->assertExitCode(0);
});

it('publishes all notifyre files with force option', function () {
    $this->artisan('notifyre:publish', ['--force' => true])
        ->expectsOutput('Publishing Notifyre files...')
        ->expectsOutput('All Notifyre files published successfully!')
        ->assertExitCode(0);
});

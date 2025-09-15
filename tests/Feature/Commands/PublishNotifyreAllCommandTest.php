<?php

use Illuminate\Support\Facades\File;

it('publishes all notifyre files without force', function () {
    $configPath = config_path('notifyre.php');
    File::ensureDirectoryExists(dirname($configPath));
    File::put($configPath, "<?php\nreturn [];\n");

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

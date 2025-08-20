<?php

namespace Arbi\Notifyre\Commands;

use Illuminate\Console\Command;

class PublishNotifyreAllCommand extends Command
{
    protected $signature = 'notifyre:publish {--force : Force the operation to run without confirmation}';

    protected $description = 'Publish all Notifyre files (config and environment variables)';

    public function handle(): void
    {
        $force = $this->option('force') ? ['--force' => true] : [];

        $this->info('Publishing Notifyre files...');

        $this->call('notifyre:publish-config', $force);
        $this->call('notifyre:publish-env', $force);

        $this->info('All Notifyre files published successfully!');
        $this->newLine();
        $this->line('Next steps:');
        $this->line('1. Update NOTIFYRE_API_TOKEN in your .env file');
        $this->line('2. Set NOTIFYRE_DRIVER=sms for production use');
        $this->line('3. Test with: php artisan sms:send "" "" "Test message"');
    }
}

<?php

namespace MagicSystemsIO\Notifyre\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishNotifyreConfigCommand extends Command
{
    protected $signature = 'notifyre:publish-config {--force : Force the operation to run without confirmation}';

    protected $description = 'Publish Notifyre configuration file';

    public function handle(): void
    {
        if ($this->shouldPublishConfig()) {
            $this->call('vendor:publish', [
                '--tag' => 'notifyre-config',
                '--force' => true,
            ]);
            $this->info('Notifyre configuration published successfully to config/notifyre.php');
        }
    }

    private function shouldPublishConfig(): bool
    {
        if ($this->option('force')) {
            return true;
        }

        if (File::exists(config_path('notifyre.php'))) {
            return $this->confirm('Notifyre config file already exists. Do you want to overwrite it?');
        }

        return true;
    }
}

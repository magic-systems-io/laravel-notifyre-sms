<?php

namespace MagicSystemsIO\Notifyre\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;

class PublishNotifyreEnvCommand extends Command
{
    protected $signature = 'notifyre:publish-env {--force : Force the operation to run without confirmation}';

    protected $description = 'Publish Notifyre environment variables to .env file';

    private array $envVariables = [
        'NOTIFYRE_DRIVER' => 'sms',
        'NOTIFYRE_API_KEY' => 'your_api_key_here',
        'NOTIFYRE_DEFAULT_NUMBER_PREFIX' => '',
        'NOTIFYRE_BASE_URL' => 'https://api.notifyre.com',
        'NOTIFYRE_TIMEOUT' => 30,
        'NOTIFYRE_RETRY_TIMES' => 3,
        'NOTIFYRE_RETRY_SLEEP' => 1,
        'NOTIFYRE_ROUTES_ENABLED' => true,
        'NOTIFYRE_ROUTE_PREFIX' => 'notifyre',
        'NOTIFYRE_RATE_LIMIT_ENABLED' => true,
        'NOTIFYRE_RATE_LIMIT_MAX' => 60,
        'NOTIFYRE_RATE_LIMIT_WINDOW' => 1,
        'NOTIFYRE_DB_ENABLED' => true,
        'NOTIFYRE_LOGGING_ENABLED' => true,
        'NOTIFYRE_LOG_PREFIX' => 'notifyre_sms',
        'NOTIFYRE_WEBHOOK_RETRY_ATTEMPTS' => 3,
        'NOTIFYRE_WEBHOOK_RETRY_DELAY' => 1,
    ];

    public function handle(): void
    {
        $envPath = base_path('.env');

        if (!File::exists($envPath)) {
            $this->error('.env file not found. Please create one first.');

            return;
        }

        try {
            if (!$this->option('force') && !$this->confirm('Do you want to add Notifyre environment variables to your .env file?')) {
                return;
            }

            $this->updateExistingEnvFile($envPath);

        } catch (Exception $e) {
            $this->error('Failed to write environment variables: ' . $e->getMessage());
        }
    }

    /**
     * @throws FileNotFoundException
     */
    private function updateExistingEnvFile(string $envPath): void
    {
        $currentEnv = File::get($envPath);
        $additions = [];

        foreach ($this->envVariables as $key => $value) {
            if (!preg_match("/^$key=/m", $currentEnv)) {
                $additions[] = "$key=$value";
            }
        }

        if (empty($additions)) {
            $this->info('All Notifyre environment variables are already present in your .env file.');

            return;
        }

        $content = "\n# Notifyre Configuration\n" . implode("\n", $additions) . "\n";
        File::append($envPath, $content);

        $this->info('Added ' . count($additions) . ' Notifyre environment variables to your .env file');
        $this->line('Added variables:');
        foreach ($additions as $addition) {
            $this->line('  ' . $addition);
        }

        $this->newLine();
        $this->warn('Remember to update NOTIFYRE_API_KEY with your actual API key!');
        $this->warn('Set NOTIFYRE_DRIVER=sms for production use.');
    }
}

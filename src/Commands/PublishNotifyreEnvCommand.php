<?php

namespace Arbi\Notifyre\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;

class PublishNotifyreEnvCommand extends Command
{
    protected $signature = 'notifyre:publish-env {--force : Force the operation to run without confirmation}';

    protected $description = 'Publish Notifyre environment variables to .env file';

    private array $envVariables = [
        'NOTIFYRE_API_TOKEN' => 'your_api_token_here',
        'NOTIFYRE_DRIVER' => 'log',
        'NOTIFYRE_TIMEOUT' => '30',
        'NOTIFYRE_RETRY_TIMES' => '3',
        'NOTIFYRE_RETRY_SLEEP' => '1000',
        'NOTIFYRE_BASE_URL' => 'https://api.notifyre.com',
        'NOTIFYRE_SMS_SENDER' => 'YourAppName',
        'NOTIFYRE_SMS_RECIPIENT' => '+1234567890',
        'NOTIFYRE_DEFAULT_NUMBER_PREFIX' => '+1',
        'NOTIFYRE_SMS_DELAY' => '1',
        'NOTIFYRE_MAX_PER_MINUTE' => '60',
        'NOTIFYRE_CACHE_ENABLED' => 'true',
        'NOTIFYRE_CACHE_TTL' => '3600',
        'NOTIFYRE_CACHE_PREFIX' => 'notifyre_',
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
        $this->warn('Remember to update NOTIFYRE_API_TOKEN with your actual API key!');
        $this->warn('Set NOTIFYRE_DRIVER=sms for production use.');
    }
}

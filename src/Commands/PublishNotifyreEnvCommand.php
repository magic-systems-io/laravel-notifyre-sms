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
        'NOTIFYRE_API_KEY' => 'your_api_key_here',
        'NOTIFYRE_WEBHOOK_SECRET' => 'your_webhook_secret_here',
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
        $this->warn('Remember to update NOTIFYRE_API_KEY with your actual API key, and NOTIFYRE_WEBHOOK_SECRET with your actual webhook secret!');
    }
}

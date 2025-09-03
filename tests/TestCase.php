<?php

namespace MagicSystemsIO\Notifyre\Tests;

use MagicSystemsIO\Notifyre\Facades\Notifyre;
use MagicSystemsIO\Notifyre\Providers\NotifyreServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * Get package providers.
     */
    protected function getPackageProviders($app): array
    {
        return [
            NotifyreServiceProvider::class,
        ];
    }

    /**
     * Get package aliases.
     */
    protected function getPackageAliases($app): array
    {
        return [
            'Notifyre' => Notifyre::class,
        ];
    }

    /**
     * Define environment setup.
     */
    protected function defineEnvironment($app): void
    {
        // Set up testing environment
        $app['config']->set('notifyre.base_url', 'https://api.notifyre.com');
        $app['config']->set('notifyre.api_key', 'test-api-key-123');
        $app['config']->set('notifyre.timeout', 30);
        $app['config']->set('notifyre.retry.times', 3);
        $app['config']->set('notifyre.retry.sleep', 1000);

        // Set up database for testing
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /**
     * Define database migrations.
     */
    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}

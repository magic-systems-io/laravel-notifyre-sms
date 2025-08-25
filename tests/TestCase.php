<?php

namespace MagicSystemsIO\Notifyre\Tests;

use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * Get package providers.
     */
    protected function getPackageProviders($app)
    {
        return [
            \MagicSystemsIO\Notifyre\Providers\NotifyreServiceProvider::class,
        ];
    }

    /**
     * Get package aliases.
     */
    protected function getPackageAliases($app)
    {
        return [
            'Notifyre' => \MagicSystemsIO\Notifyre\Facades\Notifyre::class,
        ];
    }

    /**
     * Define environment setup.
     */
    protected function defineEnvironment($app)
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
    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}

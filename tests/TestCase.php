<?php

namespace Arbi\Notifyre\Tests;

use Arbi\Notifyre\Providers\NotifyreServiceProvider;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Facade;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected Application $app;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->app = new Application(
            $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
        );

        $config = new Repository([
            'notifyre' => [
                'driver' => 'log',
                'base_url' => 'https://api.notifyre.com',
                'api_key' => 'test-api-key',
                'timeout' => 30,
                'retry' => [
                    'times' => 3,
                    'sleep' => 1000,
                ],
                'cache' => [
                    'enabled' => false,
                ],
                'default_sender' => 'TestApp',
                'default_recipient' => '+1234567890',
            ],
            'services' => [
                'notifyre' => [
                    'api_key' => 'test-api-key',
                ],
            ],
        ]);

        $this->app->instance('config', $config);

        Facade::setFacadeApplication($this->app);

        $provider = new NotifyreServiceProvider($this->app);
        $provider->register();
        $provider->boot();
    }

    /**
     * Clean up after tests
     */
    protected function tearDown(): void
    {
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);

        parent::tearDown();
    }
}

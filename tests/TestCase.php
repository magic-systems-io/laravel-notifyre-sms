<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Set default test configuration
        config([
            'notifyre.driver' => 'log',
            'notifyre.base_url' => 'https://api.notifyre.com',
            'notifyre.api_key' => 'test-api-key',
            'notifyre.timeout' => 30,
            'notifyre.retry.times' => 3,
            'notifyre.retry.sleep' => 1000,
            'notifyre.cache.enabled' => false,
            'notifyre.default_sender' => 'TestApp',
            'notifyre.default_recipient' => '+1234567890',
        ]);
    }

    /**
     * Create a test configuration for Notifyre
     */
    protected function notifyreTestConfig(array $overrides = []): array
    {
        return array_merge([
            'driver' => 'log',
            'api_key' => 'test-api-key',
            'base_url' => 'https://api.notifyre.com',
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
        ], $overrides);
    }
}

<?php

namespace Arbi\Notifyre\Tests;

use Arbi\Notifyre\Providers\Core\NotifyreServiceProvider;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Console\Kernel;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Facade;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        // Don't call parent::setUp() to avoid Laravel bootstrap requirements

        $app = new Application(
            $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
        );

        // Bind config first
        $config = new Repository([
            'notifyre' => [
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
                    'driver' => 'log',
                    'api_key' => 'test-api-key',
                ],
            ],
        ]);

        $app->singleton('config', function () use ($config) {
            return $config;
        });

        // Bind core Laravel contracts
        $app->singleton(
            \Illuminate\Contracts\Http\Kernel::class,
            \Illuminate\Foundation\Http\Kernel::class
        );

        $app->singleton(
            \Illuminate\Contracts\Console\Kernel::class,
            Kernel::class
        );

        $app->singleton(
            ExceptionHandler::class,
            Handler::class
        );

        // Set up facades BEFORE setting the config
        Facade::setFacadeApplication($app);

        // Set up validator
        $app->singleton('validator', function () {
            return new Factory(
                new Translator(
                    new ArrayLoader(),
                    'en'
                )
            );
        });

        // Set up database connection
        $capsule = new Capsule();
        $capsule->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        $app->singleton('db', function () use ($capsule) {
            return $capsule;
        });

        $app->singleton('db.connection', function () use ($capsule) {
            return $capsule->getConnection();
        });

        // Set up Schema facade
        $app->singleton('db.schema', function () use ($capsule) {
            return $capsule->getConnection()->getSchemaBuilder();
        });

        \Illuminate\Database\Eloquent\Model::setConnectionResolver($capsule->getDatabaseManager());

        // Run migrations
        $this->runMigrations($capsule);

        $provider = new NotifyreServiceProvider($app);
        $provider->register();
        $provider->boot();

        $this->app = $app;
    }

    protected function runMigrations($capsule): void
    {
        // Create the migrations table if it doesn't exist
        $capsule->getConnection()->statement('CREATE TABLE IF NOT EXISTS migrations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            migration VARCHAR(255) NOT NULL,
            batch INTEGER NOT NULL
        )');

        // Run the notifyre migrations using raw SQL
        $capsule->getConnection()->statement('CREATE TABLE IF NOT EXISTS notifyre_sms_messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            created_at DATETIME,
            updated_at DATETIME,
            sender VARCHAR(50),
            body VARCHAR(160)
        )');

        $capsule->getConnection()->statement('CREATE TABLE IF NOT EXISTS notifyre_recipients (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            created_at DATETIME,
            updated_at DATETIME,
            type VARCHAR(50) DEFAULT "mobile_number",
            value VARCHAR(255),
            UNIQUE(type, value)
        )');

        $capsule->getConnection()->statement('CREATE TABLE IF NOT EXISTS notifyre_sms_message_recipients (
            notifyre_sms_message_id INTEGER,
            notifyre_recipient_id INTEGER,
            FOREIGN KEY (notifyre_sms_message_id) REFERENCES notifyre_sms_messages(id) ON DELETE CASCADE,
            FOREIGN KEY (notifyre_recipient_id) REFERENCES notifyre_recipients(id) ON DELETE CASCADE
        )');
    }
}

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default driver that will be used to send SMS
    | messages. You can choose between 'sms' for sending SMS via the Notifyre.
    |
    */

    'driver' => env('NOTIFYRE_DRIVER', 'sms'), // 'sms'

    /*
    |--------------------------------------------------------------------------
    | API Key
    |--------------------------------------------------------------------------
    |
    | This is your Notifyre API key used for authentication when sending SMS
    | messages via the Notifyre API. Make sure to keep this key secure.
    | You can obtain your API key from the Notifyre dashboard.
    |
    */

    'api_key' => env('NOTIFYRE_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Default Number Prefix
    |--------------------------------------------------------------------------
    |
    | This is the default prefix that will be added to recipient numbers if they
    | do not already include a country code.
    |
    */

    'default_number_prefix' => '+1',

    /*
    |--------------------------------------------------------------------------
    | HTTP Configuration
    |--------------------------------------------------------------------------
    |
    | This section contains HTTP configuration options for the Notifyre API,
    | such as the base URL, timeout settings and retry logic.
    |
    */

    'http' => [
        'base_url' => 'https://api.notifyre.com',
        'timeout' => 30, // seconds
        'retry' => [
            'times' => 3,
            'sleep' => 1, // seconds between retries
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Routes Configuration
    |--------------------------------------------------------------------------
    |
    | This section contains configuration options for the package routes,
    | such as enabling/disabling routes, setting middleware, prefix, and rate limiting.
    | You can customize these settings to control how the routes behave.
    |
    */

    'routes' => [
        'enabled' => true,
        'prefix' => 'notifyre',
        'middleware' => ['api'],
        'rate_limit' => [
            'enabled' => true,
            'max_requests' => 60, // per minute
            'decay_minutes' => 1,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Configuration
    |--------------------------------------------------------------------------
    |
    | Control whether the package should store SMS messages and recipients
    | in the database for tracking and webhook processing.
    |
    */

    'database' => [
        'enabled' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | This section contains configuration options for custom logging behavior.
    | The logger uses your application's default logging configuration but
    | adds a prefix to make it easier to identify Notifyre-related logs.
    |
    | 'level' - Set the minimum log level for Notifyre logs. Defaults to
    | NOTIFYRE_LOG_LEVEL, then LOG_LEVEL, then 'debug'. When APP_DEBUG is
    | false and no explicit level is set, it defaults to 'info'.
    | Options: emergency, alert, critical, error, warning, notice, info, debug
    |
    */

    'logging' => [
        'enabled' => true,
        'prefix' => 'notifyre_sms',
        'level' => env('NOTIFYRE_LOG_LEVEL', env('LOG_LEVEL', 'debug')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | This section contains configuration options for handling webhooks from
    | Notifyre. You can set the number of retry attempts and the delay between
    | retries in case of failures.
    |
    */

    'webhook' => [
        'secret' => env('NOTIFYRE_WEBHOOK_SECRET'),
        'retry_attempts' => 3,
        'retry_delay' => 1, // seconds between retries
    ],

];

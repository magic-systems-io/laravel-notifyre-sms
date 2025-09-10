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

    'default_number_prefix' => env('NOTIFYRE_DEFAULT_NUMBER_PREFIX', ''),

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
        'base_url' => env('NOTIFYRE_BASE_URL', 'https://api.notifyre.com'),
        'timeout' => env('NOTIFYRE_TIMEOUT', 30), // seconds
        'retry' => [
            'times' => env('NOTIFYRE_RETRY_TIMES', 3),
            'sleep' => env('NOTIFYRE_RETRY_SLEEP', 1), // seconds between retries
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
        'enabled' => env('NOTIFYRE_ROUTES_ENABLED', true),
        'prefix' => env('NOTIFYRE_ROUTE_PREFIX', 'notifyre'),
        'middleware' => ['api'],
        'rate_limit' => [
            'enabled' => env('NOTIFYRE_RATE_LIMIT_ENABLED', true),
            'max_requests' => env('NOTIFYRE_RATE_LIMIT_MAX', 60), // per minute
            'decay_minutes' => env('NOTIFYRE_RATE_LIMIT_WINDOW', 1),
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
        'enabled' => env('NOTIFYRE_DB_ENABLED', true),
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
    */

    'logging' => [
        'enabled' => env('NOTIFYRE_LOGGING_ENABLED', true),
        'prefix' => env('NOTIFYRE_LOG_PREFIX', 'notifyre_sms'),
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
        'retry_attempts' => env('NOTIFYRE_WEBHOOK_RETRY_ATTEMPTS', 3),
        'retry_delay' => env('NOTIFYRE_WEBHOOK_RETRY_DELAY', 1), // seconds between retries
    ],

];

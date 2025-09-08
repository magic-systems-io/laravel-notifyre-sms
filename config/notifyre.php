<?php


return [

    /*
    | --------------------------------------------------------------------------
    | Default Driver
    | --------------------------------------------------------------------------
    |
    | This option controls the default driver that will be used to send SMS
    | messages. You can choose between 'api' for sending SMS via the Notifyre
    | API or 'log' for logging SMS messages without sending them.
    |
    */

    'driver' => env('NOTIFYRE_DRIVER', 'sms'), // 'sms' or 'log'

    /*
     | ---------------------------------------------------------------------------
     | API Key
     | ---------------------------------------------------------------------------
     |
     | This is your Notifyre API key used for authentication when sending SMS
     | messages via the Notifyre API. Make sure to keep this key secure.
     | You can obtain your API key from the Notifyre dashboard.
     |
     */
    'api_key' => env('NOTIFYRE_API_KEY'),


    /*
     | ---------------------------------------------------------------------------
     | Default Number Prefix
     | ---------------------------------------------------------------------------
     |
     | This is the default prefix that will be added to recipient numbers if they
     | do not already include a country code.
     */
    'default_number_prefix' => env('NOTIFYRE_DEFAULT_NUMBER_PREFIX', ''),

    /*
     | ---------------------------------------------------------------------------
     | API Configuration
     | ---------------------------------------------------------------------------
     |
     | This section contains configuration options for the Notifyre API, such as
     | the base URL, timeout settings and retry logic.
     |
     */
    'base_url' => env('NOTIFYRE_BASE_URL', 'https://api.notifyre.com'),

    'timeout' => 30, // HTTP request timeout in seconds

    'retry' => [
        'times' => 3,
        'sleep' => 1000, // milliseconds between retries
    ],


    /*
     | ---------------------------------------------------------------------------
     | API Configuration
     | ---------------------------------------------------------------------------
     |
     | This section contains configuration options for the Notifyre API, such as
     | enabling/disabling the API, setting middleware, prefix, and rate limiting.
     | You can customize these settings to control how the API behaves.
     |
     */

    'api' => [
        'enabled' => env('NOTIFYRE_API_ENABLED', true),
        'prefix' => 'notifyre',
        'middleware' => 'api',
        'rate_limit' => [
            'enabled' => true,
            'max_requests' => 60, // Maximum requests per minute
            'decay_minutes' =>  1, // Time window for rate limiting in minutes
        ],
        'database' => [
            'enabled' => env('NOTIFYRE_DB_ENABLED', true),
        ],
    ],

    /*
     | ---------------------------------------------------------------------------
     | Logging Configuration
     | ---------------------------------------------------------------------------
     |
     | This section contains configuration options for custom logging behavior.
     | The logger uses your application's default logging configuration but
     | adds a prefix to make it easier to identify Notifyre-related logs.
     |
     */
    'logging' => [
        'prefix' => 'notifyre_sms',
        'enabled' => env('NOTIFYRE_LOGGING_ENABLED', true),
    ],
];

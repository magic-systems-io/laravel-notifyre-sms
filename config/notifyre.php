<?php

use Arbi\Notifyre\Enums\NotifyreDriver;

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

    'driver' => env('NOTIFYRE_DRIVER', NotifyreDriver::LOG), // 'api' or 'log'

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
    'api_key' => env('NOTIFYRE_API_KEY', ''),


    /*
     | ---------------------------------------------------------------------------
     | Default Sender
     | ---------------------------------------------------------------------------
     |
     | This is the default sender ID that will be used when sending SMS messages.
     | If you leave it empty, Notifyre will auto-assign a sender ID based on
     | your API token. You can set a custom sender ID if you have one.
     */
    'default_sender' => env('NOTIFYRE_SMS_SENDER', ''),

    /*
     | ---------------------------------------------------------------------------
     | Default Recipient
     | ---------------------------------------------------------------------------
     |
     | This is the default recipient number that will be used when sending SMS
     | messages. Use this for testing purposes or if you want to send SMS
     | messages to a specific number by default. If left empty, you must specify
     | the recipient in the notification.
     */
    'default_recipient' => env('NOTIFYRE_SMS_RECIPIENT', ''),

    /*
     | ---------------------------------------------------------------------------
     | Default Number Prefix
     | ---------------------------------------------------------------------------
     |
     | This is the default prefix that will be added to recipient numbers if they
     | do not already include a country code. For example, if you set it to '+49',
     | all numbers will be prefixed with '+49' unless they already start with a
     | country code.
     */
    'default_number_prefix' => env('NOTIFYRE_DEFAULT_NUMBER_PREFIX', ''), // e.g. '+49' for German numbers

    /*
     | ---------------------------------------------------------------------------
     | API Configuration
     | ---------------------------------------------------------------------------
     |
     | This section contains configuration options for the Notifyre API, such as
     | the base URL, timeout settings, retry logic, and rate limiting.
     |
     */
    'base_url' => env('NOTIFYRE_BASE_URL', 'https://api.notifyre.com'),

    'timeout' => env('NOTIFYRE_TIMEOUT', 30), // HTTP request timeout in seconds

    'retry' => [
        'times' => env('NOTIFYRE_RETRY_TIMES', 3),
        'sleep' => env('NOTIFYRE_RETRY_SLEEP', 1000), // milliseconds between retries
    ],

    'rate_limiting' => [
        'delay_between_sms' => env('NOTIFYRE_SMS_DELAY', 1), // seconds between SMS sends
        'max_per_minute' => env('NOTIFYRE_MAX_PER_MINUTE', 60),
    ],

    /*
     | ---------------------------------------------------------------------------
     | Cache Configuration
     | ---------------------------------------------------------------------------
     |
     | This section contains configuration options for caching SMS requests.
     | Use this if you want to store SMS responses to avoid calling the GET SMS API
     | repeatedly for the same request.
     |
     */
    'cache' => [
        'enabled' => env('NOTIFYRE_CACHE_ENABLED', true),
        'ttl' => env('NOTIFYRE_CACHE_TTL', 3600), // Time to live in seconds
        'prefix' => env('NOTIFYRE_CACHE_PREFIX', 'notifyre_'),
    ],

];

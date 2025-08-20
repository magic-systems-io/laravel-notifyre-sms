<?php

use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Pest\Expectation;

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * Create a test configuration for Notifyre
 */
function notifyreTestConfig(array $overrides = []): array
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

/**
 * Create a test RequestBodyDTO
 */
function createTestMessage(
    string $body = 'Test message',
    ?string $sender = 'TestApp',
    array $recipients = null
): RequestBodyDTO {
    if ($recipients === null) {
        $recipients = [
            new Recipient(type: 'mobile_number', value: '+1234567890'),
        ];
    }

    return new RequestBodyDTO(
        body: $body,
        sender: $sender,
        recipients: $recipients
    );
}

/**
 * Create a test Recipient
 */
function createTestRecipient(
    string $type = 'mobile_number',
    string $value = '+1234567890'
): Recipient {
    return new Recipient($type, $value);
}

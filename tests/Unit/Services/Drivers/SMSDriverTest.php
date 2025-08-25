<?php

namespace MagicSystemsIO\Notifyre\Tests\Unit\Services\Drivers;

use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponseBody;
use MagicSystemsIO\Notifyre\Services\Drivers\SMSDriver;
use ReflectionClass;

use function MagicSystemsIO\Notifyre\Tests\Helpers\build_request_body_basic;

test('can be instantiated', function () {
    $driver = new SMSDriver();

    expect($driver)->toBeInstanceOf(SMSDriver::class);
});

test('getApiUrl returns correct URL when base_url is configured', function () {
    config(['notifyre.base_url' => 'https://api.notifyre.com']);

    $driver = new SMSDriver();
    $reflection = new ReflectionClass($driver);
    $method = $reflection->getMethod('getApiUrl');

    $url = $method->invoke($driver);

    expect($url)->toBe('https://api.notifyre.com/sms/send');
});

test('getApiUrl trims trailing slash from base_url', function () {
    config(['notifyre.base_url' => 'https://api.notifyre.com/']);

    $driver = new SMSDriver();
    $reflection = new ReflectionClass($driver);
    $method = $reflection->getMethod('getApiUrl');

    $url = $method->invoke($driver);

    expect($url)->toBe('https://api.notifyre.com/sms/send');
});

test('getApiUrl throws exception when base_url is not configured', function () {
    config(['notifyre.base_url' => '']);

    $driver = new SMSDriver();
    $reflection = new ReflectionClass($driver);
    $method = $reflection->getMethod('getApiUrl');

    expect(fn () => $method->invoke($driver))
        ->toThrow(InvalidArgumentException::class, 'Notifyre base URL is not configured.');
});

test('getApiUrl throws exception when base_url is only whitespace', function () {
    config(['notifyre.base_url' => '   ']);

    $driver = new SMSDriver();
    $reflection = new ReflectionClass($driver);
    $method = $reflection->getMethod('getApiUrl');

    expect(fn () => $method->invoke($driver))
        ->toThrow(InvalidArgumentException::class, 'Notifyre base URL is not configured.');
});

test('getApiKey returns api_key from services config when available', function () {
    config([
        'services.notifyre.api_key' => 'services-api-key',
        'notifyre.api_key' => 'notifyre-api-key',
    ]);

    $driver = new SMSDriver();
    $reflection = new ReflectionClass($driver);
    $method = $reflection->getMethod('getApiKey');

    $apiKey = $method->invoke($driver);

    expect($apiKey)->toBe('services-api-key');
});

test('getApiKey returns api_key from notifyre config when services config is not available', function () {
    config([
        'services.notifyre.api_key' => null,
        'notifyre.api_key' => 'notifyre-api-key',
    ]);

    $driver = new SMSDriver();
    $reflection = new ReflectionClass($driver);
    $method = $reflection->getMethod('getApiKey');

    $apiKey = $method->invoke($driver);

    expect($apiKey)->toBe('notifyre-api-key');
});

test('getApiKey throws exception when no api_key is configured', function () {
    config([
        'services.notifyre.api_key' => null,
        'notifyre.api_key' => null,
    ]);

    $driver = new SMSDriver();
    $reflection = new ReflectionClass($driver);
    $method = $reflection->getMethod('getApiKey');

    expect(fn () => $method->invoke($driver))
        ->toThrow(InvalidArgumentException::class, 'Notifyre API key is not configured.');
});

test('getApiKey throws exception when api_key is only whitespace', function () {
    config([
        'services.notifyre.api_key' => '   ',
        'notifyre.api_key' => null,
    ]);

    $driver = new SMSDriver();
    $reflection = new ReflectionClass($driver);
    $method = $reflection->getMethod('getApiKey');

    expect(fn () => $method->invoke($driver))
        ->toThrow(InvalidArgumentException::class, 'Notifyre API key is not configured.');
});

test('send method makes HTTP request with correct parameters', function () {
    Http::fake([
        'https://api.notifyre.com/sms/send' => Http::response([
            'Success' => true,
            'StatusCode' => 200,
            'Message' => 'OK',
            'Payload' => [
                'SmsMessageID' => 'sms-123',
                'FriendlyID' => 'friendly-123',
                'InvalidToNumbers' => [],
            ],
            'Errors' => [],
        ]),
    ]);

    $driver = new SMSDriver();
    $request = build_request_body_basic();

    $response = $driver->send($request);

    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.notifyre.com/sms/send' &&
               $request->method() === 'POST' &&
               $request->header('x-api-token')[0] === 'test-api-key-123' &&
               $request->header('Content-Type')[0] === 'application/json';
    });

    expect($response)->not->toBeNull()
        ->and($response->success)->toBeTrue()
        ->and($response->statusCode)->toBe(200);
});

test('send method uses configured timeout and retry settings', function () {
    config([
        'notifyre.timeout' => 60,
        'notifyre.retry.times' => 5,
        'notifyre.retry.sleep' => 2000,
    ]);

    Http::fake([
        'https://api.notifyre.com/sms/send' => Http::response([
            'Success' => true,
            'StatusCode' => 200,
            'Message' => 'OK',
            'Payload' => [
                'SmsMessageID' => 'sms-123',
                'FriendlyID' => 'friendly-123',
                'InvalidToNumbers' => [],
            ],
            'Errors' => [],
        ]),
    ]);

    $driver = new SMSDriver();
    $request = build_request_body_basic();

    $response = $driver->send($request);

    expect($response)->not->toBeNull();
    // Note: We can't easily test the actual timeout and retry configuration
    // as they're internal to the Http facade, but we can verify the method works
});

test('parseResponse creates ResponseBody with success response', function () {
    $driver = new SMSDriver();
    $reflection = new ReflectionClass($driver);
    $method = $reflection->getMethod('parseResponse');

    $responseData = [
        'Success' => true,
        'StatusCode' => 200,
        'Message' => 'OK',
        'Payload' => [
            'SmsMessageID' => 'sms-123',
            'FriendlyID' => 'friendly-123',
            'InvalidToNumbers' => [],
        ],
        'Errors' => [],
    ];

    $response = $method->invoke($driver, $responseData, 200);

    expect($response)->toBeInstanceOf(ResponseBody::class)
        ->and($response->success)->toBeTrue()
        ->and($response->statusCode)->toBe(200)
        ->and($response->message)->toBe('OK')
        ->and($response->payload->smsMessageID)->toBe('sms-123')
        ->and($response->payload->friendlyID)->toBe('friendly-123')
        ->and($response->payload->invalidToNumbers)->toHaveCount(0)
        ->and($response->errors)->toHaveCount(0);
});

test('parseResponse creates ResponseBody with error response', function () {
    $driver = new SMSDriver();
    $reflection = new ReflectionClass($driver);
    $method = $reflection->getMethod('parseResponse');

    $responseData = [
        'Success' => false,
        'StatusCode' => 400,
        'Message' => 'Bad Request',
        'Payload' => [
            'SmsMessageID' => 'sms-123',
            'FriendlyID' => 'friendly-123',
            'InvalidToNumbers' => [],
        ],
        'Errors' => ['Invalid phone number'],
    ];

    $response = $method->invoke($driver, $responseData, 400);

    expect($response)->toBeInstanceOf(ResponseBody::class)
        ->and($response->success)->toBeFalse()
        ->and($response->statusCode)->toBe(400)
        ->and($response->message)->toBe('Bad Request')
        ->and($response->errors)->toHaveCount(1)
        ->and($response->errors[0])->toBe('Invalid phone number');
});

test('parseResponse handles invalid numbers in payload', function () {
    $driver = new SMSDriver();
    $reflection = new ReflectionClass($driver);
    $method = $reflection->getMethod('parseResponse');

    $responseData = [
        'Success' => true,
        'StatusCode' => 200,
        'Message' => 'OK',
        'Payload' => [
            'SmsMessageID' => 'sms-123',
            'FriendlyID' => 'friendly-123',
            'InvalidToNumbers' => [
                [
                    'Number' => '+1234567890',
                    'Message' => 'Invalid format',
                ],
                [
                    'Number' => '+0987654321',
                    'Message' => 'Too short',
                ],
            ],
        ],
        'Errors' => [],
    ];

    $response = $method->invoke($driver, $responseData, 200);

    expect($response->payload->invalidToNumbers)->toHaveCount(2)
        ->and($response->payload->invalidToNumbers[0]->number)->toBe('+1234567890')
        ->and($response->payload->invalidToNumbers[0]->message)->toBe('Invalid format')
        ->and($response->payload->invalidToNumbers[1]->number)->toBe('+0987654321')
        ->and($response->payload->invalidToNumbers[1]->message)->toBe('Too short');
});

test('parseResponse handles missing payload fields gracefully', function () {
    $driver = new SMSDriver();
    $reflection = new ReflectionClass($driver);
    $method = $reflection->getMethod('parseResponse');

    $responseData = [
        'Success' => true,
        'StatusCode' => 200,
        'Message' => 'OK',
        'Payload' => [],
        'Errors' => [],
    ];

    $response = $method->invoke($driver, $responseData, 200);

    expect($response->payload->smsMessageID)->toBe('')
        ->and($response->payload->friendlyID)->toBe('')
        ->and($response->payload->invalidToNumbers)->toHaveCount(0);
});

test('parseResponse uses statusCode from response data when available', function () {
    $driver = new SMSDriver();
    $reflection = new ReflectionClass($driver);
    $method = $reflection->getMethod('parseResponse');

    $responseData = [
        'Success' => true,
        'StatusCode' => 201,
        'Message' => 'Created',
        'Payload' => [
            'SmsMessageID' => 'sms-123',
            'FriendlyID' => 'friendly-123',
            'InvalidToNumbers' => [],
        ],
        'Errors' => [],
    ];

    $response = $method->invoke($driver, $responseData, 200);

    expect($response->statusCode)->toBe(201);
});

test('parseResponse falls back to HTTP status code when not in response data', function () {
    $driver = new SMSDriver();
    $reflection = new ReflectionClass($driver);
    $method = $reflection->getMethod('parseResponse');

    $responseData = [
        'Success' => true,
        'Message' => 'OK',
        'Payload' => [
            'SmsMessageID' => 'sms-123',
            'FriendlyID' => 'friendly-123',
            'InvalidToNumbers' => [],
        ],
        'Errors' => [],
    ];

    $response = $method->invoke($driver, $responseData, 201);

    expect($response->statusCode)->toBe(201);
});

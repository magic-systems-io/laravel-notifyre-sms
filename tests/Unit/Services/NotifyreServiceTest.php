<?php

namespace MagicSystemsIO\Notifyre\Tests\Unit\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use MagicSystemsIO\Notifyre\Enums\NotifyreDriver;
use MagicSystemsIO\Notifyre\Services\Drivers\LogDriver;
use MagicSystemsIO\Notifyre\Services\Drivers\SMSDriver;
use MagicSystemsIO\Notifyre\Services\NotifyreService;
use ReflectionClass;

use function MagicSystemsIO\Notifyre\Tests\Helpers\build_request_body_basic;
use function MagicSystemsIO\Notifyre\Tests\Helpers\build_request_body_multiple_recipients;
use function MagicSystemsIO\Notifyre\Tests\Helpers\build_request_body_with_sender;

test('can be instantiated', function () {
    $service = new NotifyreService();

    expect($service)->toBeInstanceOf(NotifyreService::class);
});

test('getDriverName method returns log when log driver is configured', function () {
    config(['services.notifyre.driver' => 'log']);

    $reflection = new ReflectionClass(NotifyreService::class);
    $method = $reflection->getMethod('getDriverName');

    $driverName = $method->invoke(null);

    expect($driverName)->toBe('log');
});

test('getDriverName method returns sms when sms driver is configured', function () {
    config(['services.notifyre.driver' => 'sms']);

    $reflection = new ReflectionClass(NotifyreService::class);
    $method = $reflection->getMethod('getDriverName');

    $driverName = $method->invoke(null);

    expect($driverName)->toBe('sms');
});

test('getDriverName method uses notifyre config when services config is not available', function () {
    config([
        'services.notifyre.driver' => null,
        'notifyre.driver' => 'log',
    ]);

    $reflection = new ReflectionClass(NotifyreService::class);
    $method = $reflection->getMethod('getDriverName');

    $driverName = $method->invoke(null);

    expect($driverName)->toBe('log');
});

test('getDriverName method throws exception when no driver is configured', function () {
    config([
        'services.notifyre.driver' => null,
        'notifyre.driver' => null,
    ]);

    $reflection = new ReflectionClass(NotifyreService::class);
    $method = $reflection->getMethod('getDriverName');

    expect(fn () => $method->invoke(null))
        ->toThrow(InvalidArgumentException::class, "Invalid Notifyre driver ''. Supported drivers are: " . implode(', ', NotifyreDriver::values()));
});

test('getDriverName method throws exception when driver is empty string', function () {
    config(['services.notifyre.driver' => '']);

    $reflection = new ReflectionClass(NotifyreService::class);
    $method = $reflection->getMethod('getDriverName');

    expect(fn () => $method->invoke(null))
        ->toThrow(InvalidArgumentException::class, "Invalid Notifyre driver ''. Supported drivers are: " . implode(', ', NotifyreDriver::values()));
});

test('getDriverName method throws exception when driver is only whitespace', function () {
    config(['services.notifyre.driver' => '   ']);

    $reflection = new ReflectionClass(NotifyreService::class);
    $method = $reflection->getMethod('getDriverName');

    expect(fn () => $method->invoke(null))
        ->toThrow(InvalidArgumentException::class, "Invalid Notifyre driver ''. Supported drivers are: " . implode(', ', NotifyreDriver::values()));
});

test('getDriverName method throws exception when invalid driver is configured', function () {
    config(['services.notifyre.driver' => 'invalid-driver']);

    $reflection = new ReflectionClass(NotifyreService::class);
    $method = $reflection->getMethod('getDriverName');

    expect(fn () => $method->invoke(null))
        ->toThrow(InvalidArgumentException::class, "Invalid Notifyre driver 'invalid-driver'. Supported drivers are: " . implode(', ', NotifyreDriver::values()));
});

test('getDriverName method throws exception when driver is null string', function () {
    config(['services.notifyre.driver' => 'null']);

    $reflection = new ReflectionClass(NotifyreService::class);
    $method = $reflection->getMethod('getDriverName');

    expect(fn () => $method->invoke(null))
        ->toThrow(InvalidArgumentException::class, "Invalid Notifyre driver 'null'. Supported drivers are: " . implode(', ', NotifyreDriver::values()));
});

test('createDriver method returns LogDriver when log driver is provided', function () {
    $reflection = new ReflectionClass(NotifyreService::class);
    $method = $reflection->getMethod('createDriver');

    $driver = $method->invoke(null, 'log');

    expect($driver)->toBeInstanceOf(LogDriver::class);
});

test('createDriver method returns SMSDriver when sms driver is provided', function () {
    $reflection = new ReflectionClass(NotifyreService::class);
    $method = $reflection->getMethod('createDriver');

    $driver = $method->invoke(null, 'sms');

    expect($driver)->toBeInstanceOf(SMSDriver::class);
});

test('send method delegates to LogDriver when log driver is configured', function () {
    config(['services.notifyre.driver' => 'log']);

    $request = build_request_body_basic();

    $response = NotifyreService::send($request);

    expect($response)->toBeArray()
        ->and($response['message'])->toBe('Message sent via the log driver')
        ->and($response['request'])->toBe($request->toArray())
        ->and($response['response'])->toBeNull();
});

test('send method delegates to SMSDriver when sms driver is configured', function () {
    config([
        'services.notifyre.driver' => 'sms',
        'notifyre.api.database.enabled' => false,
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

    $request = build_request_body_basic();

    $response = NotifyreService::send($request);

    expect($response)->not->toBeNull()
        ->and($response)->toBeArray();
});

test('send method passes through ConnectionException from SMSDriver', function () {
    config(['services.notifyre.driver' => 'sms']);

    $request = build_request_body_basic();

    config(['services.notifyre.api_key' => 'invalid-key']);

    expect(fn () => NotifyreService::send($request))
        ->toThrow(RequestException::class);
});

test('send method passes through InvalidArgumentException from SMSDriver', function () {
    config(['services.notifyre.driver' => 'sms']);

    $request = build_request_body_basic();

    config(['notifyre.base_url' => '']);

    expect(fn () => NotifyreService::send($request))
        ->toThrow(InvalidArgumentException::class, 'Notifyre base URL is not configured.');
});

test('send method works with different request body configurations', function () {
    config(['services.notifyre.driver' => 'log']);

    $basicRequest = build_request_body_basic();
    $response = NotifyreService::send($basicRequest);
    expect($response)->toBeArray()
        ->and($response['message'])->toBe('Message sent via the log driver');

    $requestWithSender = build_request_body_with_sender();
    $response = NotifyreService::send($requestWithSender);
    expect($response)->toBeArray()
        ->and($response['message'])->toBe('Message sent via the log driver');

    $requestWithMultipleRecipients = build_request_body_multiple_recipients();
    $response = NotifyreService::send($requestWithMultipleRecipients);
    expect($response)->toBeArray()
        ->and($response['message'])->toBe('Message sent via the log driver');
});

test('driver configuration priority follows Laravel convention', function () {
    config([
        'services.notifyre.driver' => 'sms',
        'notifyre.driver' => 'log',
    ]);

    $reflection = new ReflectionClass(NotifyreService::class);
    $getDriverNameMethod = $reflection->getMethod('getDriverName');
    $createDriverMethod = $reflection->getMethod('createDriver');

    $driverName = $getDriverNameMethod->invoke(null);
    $driver = $createDriverMethod->invoke(null, $driverName);

    expect($driverName)->toBe('sms')
        ->and($driver)->toBeInstanceOf(SMSDriver::class);
});

test('error message includes all supported driver values', function () {
    config(['services.notifyre.driver' => 'unsupported']);

    $reflection = new ReflectionClass(NotifyreService::class);
    $method = $reflection->getMethod('getDriverName');

    $expectedDrivers = implode(', ', NotifyreDriver::values());

    expect(fn () => $method->invoke(null))
        ->toThrow(InvalidArgumentException::class, "Invalid Notifyre driver 'unsupported'. Supported drivers are: $expectedDrivers");
});

test('send method handles database persistence when enabled', function () {
    config([
        'services.notifyre.driver' => 'sms',
        'notifyre.api.database.enabled' => true,
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

    $request = build_request_body_basic();

    // This test assumes SMSMessagePersister::persist() is working correctly
    // You might need to mock this service depending on your implementation
    $response = NotifyreService::send($request);

    expect($response)->not->toBeNull();
});

test('send method returns response array when database persistence is disabled', function () {
    config([
        'services.notifyre.driver' => 'sms',
        'notifyre.api.database.enabled' => false,
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

    $request = build_request_body_basic();

    $response = NotifyreService::send($request);

    expect($response)->toBeArray()
        ->and($request)->not->toBeEmpty();
});

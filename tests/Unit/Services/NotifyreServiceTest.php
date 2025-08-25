<?php

namespace MagicSystemsIO\Notifyre\Tests\Unit\Services;

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

test('create method returns LogDriver when log driver is configured', function () {
    config(['services.notifyre.driver' => 'log']);

    $service = new NotifyreService();
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('create');

    $driver = $method->invoke($service);

    expect($driver)->toBeInstanceOf(LogDriver::class);
});

test('create method returns SMSDriver when sms driver is configured', function () {
    config(['services.notifyre.driver' => 'sms']);

    $service = new NotifyreService();
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('create');

    $driver = $method->invoke($service);

    expect($driver)->toBeInstanceOf(SMSDriver::class);
});

test('create method uses notifyre config when services config is not available', function () {
    config([
        'services.notifyre.driver' => null,
        'notifyre.driver' => 'log',
    ]);

    $service = new NotifyreService();
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('create');

    $driver = $method->invoke($service);

    expect($driver)->toBeInstanceOf(LogDriver::class);
});

test('create method throws exception when no driver is configured', function () {
    config([
        'services.notifyre.driver' => null,
        'notifyre.driver' => null,
    ]);

    $service = new NotifyreService();
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('create');

    expect(fn () => $method->invoke($service))
        ->toThrow(InvalidArgumentException::class, "Invalid Notifyre driver ''. Supported drivers are: " . implode(', ', NotifyreDriver::values()));
});

test('create method throws exception when driver is empty string', function () {
    config(['services.notifyre.driver' => '']);

    $service = new NotifyreService();
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('create');

    expect(fn () => $method->invoke($service))
        ->toThrow(InvalidArgumentException::class, "Invalid Notifyre driver ''. Supported drivers are: " . implode(', ', NotifyreDriver::values()));
});

test('create method throws exception when driver is only whitespace', function () {
    config(['services.notifyre.driver' => '   ']);

    $service = new NotifyreService();
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('create');

    expect(fn () => $method->invoke($service))
        ->toThrow(InvalidArgumentException::class, "Invalid Notifyre driver '   '. Supported drivers are: " . implode(', ', NotifyreDriver::values()));
});

test('create method throws exception when invalid driver is configured', function () {
    config(['services.notifyre.driver' => 'invalid-driver']);

    $service = new NotifyreService();
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('create');

    expect(fn () => $method->invoke($service))
        ->toThrow(InvalidArgumentException::class, "Invalid Notifyre driver 'invalid-driver'. Supported drivers are: " . implode(', ', NotifyreDriver::values()));
});

test('create method throws exception when driver is null string', function () {
    config(['services.notifyre.driver' => 'null']);

    $service = new NotifyreService();
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('create');

    expect(fn () => $method->invoke($service))
        ->toThrow(InvalidArgumentException::class, "Invalid Notifyre driver 'null'. Supported drivers are: " . implode(', ', NotifyreDriver::values()));
});

test('send method delegates to LogDriver when log driver is configured', function () {
    config(['services.notifyre.driver' => 'log']);

    $service = new NotifyreService();
    $request = build_request_body_basic();

    $response = $service->send($request);

    expect($response)->toBeNull();
});

test('send method delegates to SMSDriver when sms driver is configured', function () {
    config(['services.notifyre.driver' => 'sms']);

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

    $service = new NotifyreService();
    $request = build_request_body_basic();

    $response = $service->send($request);

    expect($response)->not->toBeNull();
});

test('send method passes through ConnectionException from SMSDriver', function () {
    config(['services.notifyre.driver' => 'sms']);

    $service = new NotifyreService();
    $request = build_request_body_basic();

    config(['services.notifyre.api_key' => 'invalid-key']);

    expect(fn () => $service->send($request))
        ->toThrow(\Illuminate\Http\Client\RequestException::class);
});

test('send method passes through InvalidArgumentException from SMSDriver', function () {
    config(['services.notifyre.driver' => 'sms']);

    $service = new NotifyreService();
    $request = build_request_body_basic();

    config(['notifyre.base_url' => '']);

    expect(fn () => $service->send($request))
        ->toThrow(InvalidArgumentException::class, 'Notifyre base URL is not configured.');
});

test('send method works with different request body configurations', function () {
    config(['services.notifyre.driver' => 'log']);

    $service = new NotifyreService();

    $basicRequest = build_request_body_basic();
    $response = $service->send($basicRequest);
    expect($response)->toBeNull();

    $requestWithSender = build_request_body_with_sender();
    $response = $service->send($requestWithSender);
    expect($response)->toBeNull();

    $requestWithMultipleRecipients = build_request_body_multiple_recipients();
    $response = $service->send($requestWithMultipleRecipients);
    expect($response)->toBeNull();
});

test('driver configuration priority follows Laravel convention', function () {
    config([
        'services.notifyre.driver' => 'sms',
        'notifyre.driver' => 'log',
    ]);

    $service = new NotifyreService();
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('create');

    $driver = $method->invoke($service);

    expect($driver)->toBeInstanceOf(SMSDriver::class);
});

test('error message includes all supported driver values', function () {
    config(['services.notifyre.driver' => 'unsupported']);

    $service = new NotifyreService();
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('create');

    $expectedDrivers = implode(', ', NotifyreDriver::values());

    expect(fn () => $method->invoke($service))
        ->toThrow(InvalidArgumentException::class, "Invalid Notifyre driver 'unsupported'. Supported drivers are: {$expectedDrivers}");
});

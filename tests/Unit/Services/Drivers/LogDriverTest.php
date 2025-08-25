<?php

namespace MagicSystemsIO\Notifyre\Tests\Unit\Services\Drivers;

use Illuminate\Support\Facades\Log;
use MagicSystemsIO\Notifyre\Contracts\NotifyreManager;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
use MagicSystemsIO\Notifyre\Services\Drivers\LogDriver;
use Mockery;

use function MagicSystemsIO\Notifyre\Tests\Helpers\build_request_body_basic;
use function MagicSystemsIO\Notifyre\Tests\Helpers\build_request_body_multiple_recipients;
use function MagicSystemsIO\Notifyre\Tests\Helpers\build_request_body_with_sender;

test('can be instantiated', function () {
    $driver = new LogDriver();

    expect($driver)->toBeInstanceOf(LogDriver::class);
});

test('implements NotifyreManager interface', function () {
    $driver = new LogDriver();

    expect($driver)->toBeInstanceOf(NotifyreManager::class);
});

test('send method returns null', function () {
    Log::shouldReceive('info')
        ->once()
        ->andReturnNull();

    $driver = new LogDriver();
    $request = build_request_body_basic();

    $response = $driver->send($request);

    expect($response)->toBeNull();
});

test('send method calls Log::info with correct message', function () {
    Log::shouldReceive('info')
        ->once()
        ->with('SMS would be sent via Notifyre', Mockery::type('array'))
        ->andReturnNull();

    $driver = new LogDriver();
    $request = build_request_body_basic();

    $response = $driver->send($request);

    expect($response)->toBeNull();
});

test('send method logs request with sender when provided', function () {
    Log::shouldReceive('info')
        ->once()
        ->with('SMS would be sent via Notifyre', Mockery::on(function ($context) {
            return $context['body'] === 'Hello, this is a test SMS message!' &&
                   $context['sender'] === '+61487654321' &&
                   is_array($context['recipients']) &&
                   count($context['recipients']) === 1;
        }))
        ->andReturnNull();

    $driver = new LogDriver();
    $request = build_request_body_with_sender();

    $response = $driver->send($request);

    expect($response)->toBeNull();
});

test('send method logs multiple recipients correctly', function () {
    Log::shouldReceive('info')
        ->once()
        ->with('SMS would be sent via Notifyre', Mockery::on(function ($context) {
            return $context['body'] === 'Hello, this is a test SMS message for multiple recipients!' &&
                   $context['sender'] === '(auto-assigned by token)' &&
                   is_array($context['recipients']) &&
                   count($context['recipients']) === 3;
        }))
        ->andReturnNull();

    $driver = new LogDriver();
    $request = build_request_body_multiple_recipients();

    $response = $driver->send($request);

    expect($response)->toBeNull();
});

test('send method logs recipient details correctly', function () {
    Log::shouldReceive('info')
        ->once()
        ->with('SMS would be sent via Notifyre', Mockery::on(function ($context) {
            $recipients = $context['recipients'];

            return $context['body'] === 'Hello, this is a test SMS message!' &&
                   is_array($recipients) &&
                   count($recipients) === 1 &&
                   $recipients[0]['type'] === 'virtual_mobile_number' &&
                   $recipients[0]['value'] === '+61412345678';
        }))
        ->andReturnNull();

    $driver = new LogDriver();
    $request = build_request_body_basic();

    $response = $driver->send($request);

    expect($response)->toBeNull();
});

test('send method logs multiple recipients with correct structure', function () {
    Log::shouldReceive('info')
        ->once()
        ->with('SMS would be sent via Notifyre', Mockery::on(function ($context) {
            $recipients = $context['recipients'];

            return $context['body'] === 'Hello, this is a test SMS message for multiple recipients!' &&
                   is_array($recipients) &&
                   count($recipients) === 3 &&
                   $recipients[0]['type'] === 'virtual_mobile_number' &&
                   $recipients[0]['value'] === '+61412345678' &&
                   $recipients[1]['type'] === 'contact' &&
                   $recipients[1]['value'] === 'contact-123' &&
                   $recipients[2]['type'] === 'group' &&
                   $recipients[2]['value'] === 'group-456';
        }))
        ->andReturnNull();

    $driver = new LogDriver();
    $request = build_request_body_multiple_recipients();

    $response = $driver->send($request);

    expect($response)->toBeNull();
});

test('send method logs empty sender as auto-assigned when null', function () {
    Log::shouldReceive('info')
        ->once()
        ->with('SMS would be sent via Notifyre', Mockery::on(function ($context) {
            return $context['sender'] === '(auto-assigned by token)';
        }))
        ->andReturnNull();

    $driver = new LogDriver();
    $request = build_request_body_basic();

    $response = $driver->send($request);

    expect($response)->toBeNull();
});

test('send method logs empty sender as auto-assigned when empty string', function () {
    Log::shouldReceive('info')
        ->once()
        ->with('SMS would be sent via Notifyre', Mockery::on(function ($context) {
            return $context['sender'] === '(auto-assigned by token)';
        }))
        ->andReturnNull();

    $driver = new LogDriver();
    $request = new RequestBody(
        body: 'Test message',
        recipients: build_request_body_basic()->recipients,
        sender: ''
    );

    $response = $driver->send($request);

    expect($response)->toBeNull();
});

test('send method logs whitespace sender as auto-assigned', function () {
    Log::shouldReceive('info')
        ->once()
        ->with('SMS would be sent via Notifyre', Mockery::on(function ($context) {
            return $context['sender'] === '(auto-assigned by token)';
        }))
        ->andReturnNull();

    $driver = new LogDriver();
    $request = new RequestBody(
        body: 'Test message',
        recipients: build_request_body_basic()->recipients,
        sender: '   '
    );

    $response = $driver->send($request);

    expect($response)->toBeNull();
});

test('send method logs actual sender when provided', function () {
    Log::shouldReceive('info')
        ->once()
        ->with('SMS would be sent via Notifyre', Mockery::on(function ($context) {
            return $context['sender'] === '+61487654321';
        }))
        ->andReturnNull();

    $driver = new LogDriver();
    $request = build_request_body_with_sender();

    $response = $driver->send($request);

    expect($response)->toBeNull();
});

test('send method logs long message body correctly', function () {
    Log::shouldReceive('info')
        ->once()
        ->with('SMS would be sent via Notifyre', Mockery::on(function ($context) {
            return $context['body'] === str_repeat('This is a very long message. ', 50) &&
                   strlen($context['body']) > 100;
        }))
        ->andReturnNull();

    $driver = new LogDriver();
    $request = new RequestBody(
        body: str_repeat('This is a very long message. ', 50),
        recipients: build_request_body_basic()->recipients
    );

    $response = $driver->send($request);

    expect($response)->toBeNull();
});

test('send method logs special characters in message body correctly', function () {
    Log::shouldReceive('info')
        ->once()
        ->with('SMS would be sent via Notifyre', Mockery::on(function ($context) {
            return $context['body'] === 'Message with special chars: @#$%^&*()_+-=[]{}|;:,.<>?';
        }))
        ->andReturnNull();

    $driver = new LogDriver();
    $request = new RequestBody(
        body: 'Message with special chars: @#$%^&*()_+-=[]{}|;:,.<>?',
        recipients: build_request_body_basic()->recipients
    );

    $response = $driver->send($request);

    expect($response)->toBeNull();
});

test('send method logs newlines and tabs in message body correctly', function () {
    Log::shouldReceive('info')
        ->once()
        ->with('SMS would be sent via Notifyre', Mockery::on(function ($context) {
            return $context['body'] === "Line 1\nLine 2\tTabbed content";
        }))
        ->andReturnNull();

    $driver = new LogDriver();
    $request = new RequestBody(
        body: "Line 1\nLine 2\tTabbed content",
        recipients: build_request_body_basic()->recipients
    );

    $response = $driver->send($request);

    expect($response)->toBeNull();
});

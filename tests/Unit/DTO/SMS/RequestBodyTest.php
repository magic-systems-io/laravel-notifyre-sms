<?php

namespace MagicSystemsIO\Notifyre\Tests\Unit\DTO\SMS;

use InvalidArgumentException;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;

use function MagicSystemsIO\Notifyre\Tests\Helpers\build_request_body_basic;
use function MagicSystemsIO\Notifyre\Tests\Helpers\build_request_body_empty_body;
use function MagicSystemsIO\Notifyre\Tests\Helpers\build_request_body_empty_recipients;
use function MagicSystemsIO\Notifyre\Tests\Helpers\build_request_body_long_message;
use function MagicSystemsIO\Notifyre\Tests\Helpers\build_request_body_multiple_recipients;
use function MagicSystemsIO\Notifyre\Tests\Helpers\build_request_body_whitespace_body;
use function MagicSystemsIO\Notifyre\Tests\Helpers\build_request_body_with_sender;

test('can be instantiated with basic request', function () {
    $request_body = build_request_body_basic();

    expect($request_body)->toBeInstanceOf(RequestBody::class)
        ->and($request_body->body)->toBe('Hello, this is a test SMS message!')
        ->and($request_body->recipients)->toHaveCount(1)
        ->and($request_body->sender)->toBeEmpty();
});

test('can be instantiated with sender', function () {
    $request_body = build_request_body_with_sender();

    expect($request_body)->toBeInstanceOf(RequestBody::class)
        ->and($request_body->body)->toBe('Hello, this is a test SMS message!')
        ->and($request_body->recipients)->toHaveCount(1)
        ->and($request_body->sender)->toBe('+61487654321');
});

test('can be instantiated with multiple recipients', function () {
    $request_body = build_request_body_multiple_recipients();

    expect($request_body)->toBeInstanceOf(RequestBody::class)
        ->and($request_body->body)->toBe('Hello, this is a test SMS message for multiple recipients!')
        ->and($request_body->recipients)->toHaveCount(3)
        ->and($request_body->sender)->toBeEmpty();
});

test('can be instantiated with long message', function () {
    $request_body = build_request_body_long_message();

    expect($request_body)->toBeInstanceOf(RequestBody::class)
        ->and($request_body->body)->toContain('This is a very long message')
        ->and($request_body->recipients)->toHaveCount(1);
});

test('toArray method works with basic request', function () {
    $request_body = build_request_body_basic();
    $array = $request_body->toArray();

    expect($array)->toBeArray()
        ->and($array)->toHaveKeys(['Body', 'Recipients'])
        ->and($array['From'])->toBeEmpty()
        ->and($array['Body'])->toBe('Hello, this is a test SMS message!')
        ->and($array['Recipients'])->toHaveCount(1);
});

test('toArray method works with sender', function () {
    $request_body = build_request_body_with_sender();
    $array = $request_body->toArray();

    expect($array)->toBeArray()
        ->and($array)->toHaveKeys(['Body', 'Recipients', 'From'])
        ->and($array['From'])->toBe('+61487654321');
});

test('toArray method works with multiple recipients', function () {
    $request_body = build_request_body_multiple_recipients();
    $array = $request_body->toArray();

    expect($array)->toBeArray()
        ->and($array['Recipients'])->toHaveCount(3)
        ->and($array['Recipients'][0])->toHaveKeys(['type', 'value']);
});


test('throws exception for empty body', function () {
    expect(fn () => build_request_body_empty_body())
        ->toThrow(InvalidArgumentException::class, 'Body cannot be empty');
});

test('throws exception for whitespace body', function () {
    expect(fn () => build_request_body_whitespace_body())
        ->toThrow(InvalidArgumentException::class, 'Body cannot be empty');
});

test('throws exception for empty recipients', function () {
    expect(fn () => build_request_body_empty_recipients())
        ->toThrow(InvalidArgumentException::class, 'Recipients cannot be empty');
});

test('can handle body with special characters', function () {
    $request_body = new RequestBody(
        body: 'Hello! This message has special chars: @#$%^&*()_+-=[]{}|;:,.<>?',
        recipients: build_request_body_basic()->recipients
    );

    expect($request_body->body)->toBe('Hello! This message has special chars: @#$%^&*()_+-=[]{}|;:,.<>?');
});

test('can handle body with newlines and tabs', function () {
    $request_body = new RequestBody(
        body: "Line 1\nLine 2\tTabbed content",
        recipients: build_request_body_basic()->recipients
    );

    expect($request_body->body)->toBe("Line 1\nLine 2\tTabbed content");
});

test('sender is optional and can be null', function () {
    $request_body = new RequestBody(
        body: 'Test message',
        recipients: build_request_body_basic()->recipients
    );

    expect($request_body->sender)->toBeEmpty();
});

test('sender can be empty string and will be ignored in toArray', function () {
    $request_body = new RequestBody(
        body: 'Test message',
        recipients: build_request_body_basic()->recipients,
        sender: ''
    );

    $array = $request_body->toArray();
    expect($array['From'])->toBeEmpty();
});

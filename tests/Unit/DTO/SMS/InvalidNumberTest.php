<?php

namespace MagicSystemsIO\Notifyre\Tests\Unit\DTO\SMS;

use MagicSystemsIO\Notifyre\DTO\SMS\InvalidNumber;

use function MagicSystemsIO\Notifyre\Tests\Helpers\build_invalid_number_basic;
use function MagicSystemsIO\Notifyre\Tests\Helpers\build_invalid_number_invalid_format;
use function MagicSystemsIO\Notifyre\Tests\Helpers\build_invalid_number_too_short;

test('can be instantiated with basic invalid number', function () {
    $invalid_number = build_invalid_number_basic();

    expect($invalid_number)->toBeInstanceOf(InvalidNumber::class)
        ->and($invalid_number->number)->toBe('+1234567890')
        ->and($invalid_number->message)->toBe('Invalid phone number format');
});

test('can be instantiated with invalid format error', function () {
    $invalid_number = build_invalid_number_invalid_format();

    expect($invalid_number)->toBeInstanceOf(InvalidNumber::class)
        ->and($invalid_number->number)->toBe('not-a-number')
        ->and($invalid_number->message)->toBe('Phone number must contain only digits and valid characters');
});

test('can be instantiated with too short error', function () {
    $invalid_number = build_invalid_number_too_short();

    expect($invalid_number)->toBeInstanceOf(InvalidNumber::class)
        ->and($invalid_number->number)->toBe('+123')
        ->and($invalid_number->message)->toBe('Phone number too short');
});

test('toArray method works correctly', function () {
    $invalid_number = build_invalid_number_basic();
    $array = $invalid_number->toArray();

    expect($array)->toBeArray()
        ->and($array)->toHaveKeys(['number', 'message'])
        ->and($array['number'])->toBe('+1234567890')
        ->and($array['message'])->toBe('Invalid phone number format');
});

test('can handle phone number with special characters', function () {
    $invalid_number = new InvalidNumber(
        number: '+1 (555) 123-4567',
        message: 'Invalid format with parentheses and spaces'
    );

    expect($invalid_number->number)->toBe('+1 (555) 123-4567')
        ->and($invalid_number->message)->toBe('Invalid format with parentheses and spaces');
});

test('can handle phone number with dashes', function () {
    $invalid_number = new InvalidNumber(
        number: '+1-555-123-4567',
        message: 'Invalid format with dashes'
    );

    expect($invalid_number->number)->toBe('+1-555-123-4567')
        ->and($invalid_number->message)->toBe('Invalid format with dashes');
});

test('can handle phone number with dots', function () {
    $invalid_number = new InvalidNumber(
        number: '+1.555.123.4567',
        message: 'Invalid format with dots'
    );

    expect($invalid_number->number)->toBe('+1.555.123.4567')
        ->and($invalid_number->message)->toBe('Invalid format with dots');
});

test('can handle very long error messages', function () {
    $long_message = str_repeat('This is a very long error message that explains in detail why the phone number is invalid. ', 5);
    $invalid_number = new InvalidNumber(
        number: '+1234567890',
        message: $long_message
    );

    expect($invalid_number->message)->toBe($long_message);
});

test('can handle error messages with special characters', function () {
    $invalid_number = new InvalidNumber(
        number: '+1234567890',
        message: 'Error: Invalid format! @#$%^&*()_+-=[]{}|;:,.<>?'
    );

    expect($invalid_number->message)->toBe('Error: Invalid format! @#$%^&*()_+-=[]{}|;:,.<>?');
});

test('can handle error messages with newlines and tabs', function () {
    $invalid_number = new InvalidNumber(
        number: '+1234567890',
        message: "Line 1\nLine 2\tTabbed content"
    );

    expect($invalid_number->message)->toBe("Line 1\nLine 2\tTabbed content");
});

test('can handle empty message', function () {
    $invalid_number = new InvalidNumber(
        number: '+1234567890',
        message: ''
    );

    expect($invalid_number->message)->toBe('');
});

test('can handle whitespace message', function () {
    $invalid_number = new InvalidNumber(
        number: '+1234567890',
        message: '   '
    );

    expect($invalid_number->message)->toBe('   ');
});

test('can handle international phone numbers', function () {
    $invalid_number = new InvalidNumber(
        number: '+44 20 7946 0958',
        message: 'Invalid UK phone number format'
    );

    expect($invalid_number->number)->toBe('+44 20 7946 0958')
        ->and($invalid_number->message)->toBe('Invalid UK phone number format');
});

test('can handle alphanumeric identifiers', function () {
    $invalid_number = new InvalidNumber(
        number: 'ABC123DEF',
        message: 'Invalid alphanumeric identifier'
    );

    expect($invalid_number->number)->toBe('ABC123DEF')
        ->and($invalid_number->message)->toBe('Invalid alphanumeric identifier');
});

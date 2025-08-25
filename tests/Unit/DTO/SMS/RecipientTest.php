<?php

namespace MagicSystemsIO\Notifyre\Tests\Unit\DTO\SMS;

use MagicSystemsIO\Notifyre\DTO\SMS\Recipient;
use MagicSystemsIO\Notifyre\Enums\NotifyreRecipientTypes;
use Symfony\Component\Mime\Exception\InvalidArgumentException;

use function MagicSystemsIO\Notifyre\Tests\Helpers\build_recipient_contact;
use function MagicSystemsIO\Notifyre\Tests\Helpers\build_recipient_empty_value;
use function MagicSystemsIO\Notifyre\Tests\Helpers\build_recipient_group;
use function MagicSystemsIO\Notifyre\Tests\Helpers\build_recipient_virtual_mobile;
use function MagicSystemsIO\Notifyre\Tests\Helpers\build_recipient_whitespace_value;

test('can be instantiated with virtual mobile number type', function () {
    $recipient = build_recipient_virtual_mobile();

    expect($recipient)->toBeInstanceOf(Recipient::class)
        ->and($recipient->type)->toBe(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value)
        ->and($recipient->value)->toBe('+61412345678');
});

test('can be instantiated with contact type', function () {
    $recipient = build_recipient_contact();

    expect($recipient)->toBeInstanceOf(Recipient::class)
        ->and($recipient->type)->toBe(NotifyreRecipientTypes::CONTACT->value)
        ->and($recipient->value)->toBe('contact-123');
});

test('can be instantiated with group type', function () {
    $recipient = build_recipient_group();

    expect($recipient)->toBeInstanceOf(Recipient::class)
        ->and($recipient->type)->toBe(NotifyreRecipientTypes::GROUP->value)
        ->and($recipient->value)->toBe('group-456');
});

test('toArray method works correctly', function () {
    $recipient = build_recipient_virtual_mobile();
    $array = $recipient->toArray();

    expect($array)->toBeArray()
        ->and($array)->toHaveKeys(['type', 'value'])
        ->and($array['type'])->toBe(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value)
        ->and($array['value'])->toBe('+61412345678');
});


test('throws exception for invalid recipient type', function () {
    expect(fn () => new Recipient('invalid_type', 'some_value'))
        ->toThrow(InvalidArgumentException::class);
});

test('throws exception for empty value', function () {
    expect(fn () => build_recipient_empty_value())
        ->toThrow(InvalidArgumentException::class);
});

test('throws exception for whitespace value', function () {
    expect(fn () => build_recipient_whitespace_value())
        ->toThrow(InvalidArgumentException::class);
});

test('accepts valid recipient types', function () {
    $validTypes = NotifyreRecipientTypes::values();

    foreach ($validTypes as $type) {
        $recipient = new Recipient($type, 'test-value');
        expect($recipient->type)->toBe($type);
    }
});

test('can handle phone number with special characters', function () {
    $recipient = new Recipient(
        NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value,
        '+1 (555) 123-4567'
    );

    expect($recipient->value)->toBe('+1 (555) 123-4567');
});

test('can handle contact with special characters', function () {
    $recipient = new Recipient(
        NotifyreRecipientTypes::CONTACT->value,
        'contact-name_with-dashes.and.dots'
    );

    expect($recipient->value)->toBe('contact-name_with-dashes.and.dots');
});

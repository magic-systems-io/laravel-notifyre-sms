<?php

namespace MagicSystemsIO\Notifyre\Tests\Unit\DTO\SMS;

use InvalidArgumentException;
use MagicSystemsIO\Notifyre\DTO\SMS\Recipient;
use MagicSystemsIO\Notifyre\Enums\NotifyreRecipientTypes;

describe('Recipient', function () {
    it('creates a valid recipient with virtual_mobile_number type', function () {
        $recipient = new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, '+1234567890');

        expect($recipient->type)->toBe(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value)
            ->and($recipient->value)->toBe('+1234567890');
    });

    it('creates a valid recipient with contact type', function () {
        $recipient = new Recipient(NotifyreRecipientTypes::CONTACT->value, 'contact_123');

        expect($recipient->type)->toBe(NotifyreRecipientTypes::CONTACT->value)
            ->and($recipient->value)->toBe('contact_123');
    });

    it('creates a valid recipient with group type', function () {
        $recipient = new Recipient(NotifyreRecipientTypes::GROUP->value, 'group_456');

        expect($recipient->type)->toBe(NotifyreRecipientTypes::GROUP->value)
            ->and($recipient->value)->toBe('group_456');
    });

    it('uses virtual_mobile_number as default type', function () {
        $recipient = new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, '+1234567890');

        expect($recipient->type)->toBe(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value);
    });

    it('throws exception for invalid type', function () {
        expect(fn () => new Recipient('invalid_type', '+1234567890'))
            ->toThrow(InvalidArgumentException::class, "Invalid type 'invalid_type'. Valid types are: " . implode(', ', NotifyreRecipientTypes::values()));
    });

    it('throws exception for empty value', function () {
        expect(fn () => new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, ''))
            ->toThrow(InvalidArgumentException::class, 'Value cannot be empty');

        expect(fn () => new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, '   '))
            ->toThrow(InvalidArgumentException::class, 'Value cannot be empty');
    });

    it('accepts various phone number formats', function (string $format) {
        $recipient = new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, $format);

        expect($recipient->value)->toBe($format);
    })->with([
        '+1234567890',
        '1234567890',
        '+1 (234) 567-8900',
        '+44 20 7946 0958',
        '+61 2 9876 5432',
        '+81 3-1234-5678',
        '+86 10 1234 5678',
        '+91 98765 43210',
        '+55 11 98765-4321',
        '+49 30 12345678',
    ]);

    it('accepts various contact ID formats', function (string $format) {
        $recipient = new Recipient(NotifyreRecipientTypes::CONTACT->value, $format);

        expect($recipient->value)->toBe($format);
    })->with([
        'contact_123',
        'CONTACT_456',
        'contact-789',
        'contact.012',
        'contact 345',
        '123contact',
        'contact123',
    ]);

    it('accepts various group ID formats', function (string $format) {
        $recipient = new Recipient(NotifyreRecipientTypes::GROUP->value, $format);

        expect($recipient->value)->toBe($format);
    })->with([
        'group_123',
        'GROUP_456',
        'group-789',
        'group.012',
        'group 345',
        '123group',
        'group123',
    ]);

    it('converts to array correctly', function () {
        expect(new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, '+1234567890')->toArray())->toBeArray();

        $recipient = new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, '+1234567890');
        $array = $recipient->toArray();

        expect($array)->toHaveKey('type')
            ->and($array)->toHaveKey('value')
            ->and($array['type'])->toBe(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value)
            ->and($array['value'])->toBe('+1234567890');
    });
});

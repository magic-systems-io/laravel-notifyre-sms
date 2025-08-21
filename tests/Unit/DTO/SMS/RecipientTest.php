<?php

namespace Arbi\Notifyre\Tests\Unit\DTO\SMS;

use Arbi\Notifyre\DTO\SMS\Recipient;
use Error;
use InvalidArgumentException;

describe('Recipient', function () {
    it('creates a valid recipient with mobile_number type', function () {
        $recipient = new Recipient('mobile_number', '+1234567890');

        expect($recipient->type)->toBe('mobile_number')
            ->and($recipient->value)->toBe('+1234567890');
    });

    it('creates a valid recipient with contact type', function () {
        $recipient = new Recipient('contact', 'contact123');

        expect($recipient->type)->toBe('contact')
            ->and($recipient->value)->toBe('contact123');
    });

    it('creates a valid recipient with group type', function () {
        $recipient = new Recipient('group', 'group456');

        expect($recipient->type)->toBe('group')
            ->and($recipient->value)->toBe('group456');
    });

    it('uses mobile_number as default type', function () {
        $recipient = new Recipient('mobile_number', '+1234567890');

        expect($recipient->type)->toBe('mobile_number')
            ->and($recipient->value)->toBe('+1234567890');
    });

    it('throws exception for invalid type', function () {
        expect(fn () => new Recipient('invalid_type', 'value123'))
            ->toThrow(InvalidArgumentException::class, "Invalid type 'invalid_type'. Valid types are: mobile_number, contact, group");
    });

    it('throws exception for empty value', function () {
        expect(fn () => new Recipient('mobile_number', ''))
            ->toThrow(InvalidArgumentException::class, 'Value cannot be empty');
    });

    it('throws exception for whitespace-only value', function () {
        expect(fn () => new Recipient('mobile_number', '   '))
            ->toThrow(InvalidArgumentException::class, 'Value cannot be empty');
    });

    it('accepts phone numbers with various formats', function () {
        $formats = [
            '+1234567890',
            '1234567890',
            '+1-234-567-8900',
            '(123) 456-7890',
            '123.456.7890',
        ];

        foreach ($formats as $format) {
            $recipient = new Recipient('mobile_number', $format);
            expect($recipient->value)->toBe($format);
        }
    });

    it('accepts alphanumeric values for contact type', function () {
        $values = [
            'contact123',
            'user_456',
            'customer-789',
            'client_abc_123',
        ];

        foreach ($values as $value) {
            $recipient = new Recipient('contact', $value);
            expect($recipient->value)->toBe($value);
        }
    });

    it('accepts alphanumeric values for group type', function () {
        $values = [
            'group123',
            'team_456',
            'department-789',
            'staff_abc_123',
        ];

        foreach ($values as $value) {
            $recipient = new Recipient('group', $value);
            expect($recipient->value)->toBe($value);
        }
    });


    it('is readonly', function () {
        $recipient = new Recipient('mobile_number', '+1234567890');

        expect(fn () => $recipient->type = 'contact')->toThrow(Error::class);
    });
});

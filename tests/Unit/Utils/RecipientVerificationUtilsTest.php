<?php

use MagicSystemsIO\Notifyre\Utils\RecipientVerificationUtils;

it('validates a correct international mobile number', function () {
    expect(RecipientVerificationUtils::validateRecipient('+14155552671'))->toBeTrue();
});

it('returns false for an obviously invalid mobile number', function () {
    expect(RecipientVerificationUtils::validateRecipient('1234'))->toBeFalse();
});

it('throws when value is empty or whitespace', function () {
    expect(fn () => RecipientVerificationUtils::validateRecipient(''))->toThrow(InvalidArgumentException::class)
        ->and(fn () => RecipientVerificationUtils::validateRecipient('   '))->toThrow(InvalidArgumentException::class);
});

it('throws for unsupported recipient type', function () {
    expect(fn () => RecipientVerificationUtils::validateRecipient('+14155552671', 'not_a_type'))
        ->toThrow(InvalidArgumentException::class);
});

it('accepts contact type without additional validation', function () {
    expect(RecipientVerificationUtils::validateRecipient('contact-identifier', 'contact'))->toBeTrue();
});

it('accepts group type without additional validation', function () {
    expect(RecipientVerificationUtils::validateRecipient('group-identifier', 'group'))->toBeTrue();
});

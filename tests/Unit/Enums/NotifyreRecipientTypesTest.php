<?php

namespace MagicSystemsIO\Notifyre\Tests\Unit\Enums;

use MagicSystemsIO\Notifyre\Enums\NotifyreRecipientTypes;

test('has correct case values', function () {
    expect(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value)->toBe('virtual_mobile_number')
        ->and(NotifyreRecipientTypes::CONTACT->value)->toBe('contact')
        ->and(NotifyreRecipientTypes::GROUP->value)->toBe('group');
});

test('values method returns all case values', function () {
    $values = NotifyreRecipientTypes::values();

    expect($values)->toBeArray()
        ->and($values)->toHaveCount(3)
        ->and($values)->toContain('virtual_mobile_number')
        ->and($values)->toContain('contact')
        ->and($values)->toContain('group');
});

test('isValid method returns true for valid types', function () {
    expect(NotifyreRecipientTypes::isValid('virtual_mobile_number'))->toBeTrue()
        ->and(NotifyreRecipientTypes::isValid('contact'))->toBeTrue()
        ->and(NotifyreRecipientTypes::isValid('group'))->toBeTrue();
});

test('isValid method returns false for invalid types', function () {
    expect(NotifyreRecipientTypes::isValid('invalid_type'))->toBeFalse()
        ->and(NotifyreRecipientTypes::isValid(''))->toBeFalse()
        ->and(NotifyreRecipientTypes::isValid('VIRTUAL_MOBILE_NUMBER'))->toBeFalse()
        ->and(NotifyreRecipientTypes::isValid('Contact'))->toBeFalse()
        ->and(NotifyreRecipientTypes::isValid('GROUP_'))->toBeFalse();
});

test('isValid method handles edge cases', function () {
    expect(NotifyreRecipientTypes::isValid('   '))->toBeFalse()
        ->and(NotifyreRecipientTypes::isValid('virtual_mobile_number '))->toBeFalse()
        ->and(NotifyreRecipientTypes::isValid(' virtual_mobile_number'))->toBeFalse()
        ->and(NotifyreRecipientTypes::isValid('virtual_mobile_number_'))->toBeFalse();
});

test('cases method returns all enum cases', function () {
    $cases = NotifyreRecipientTypes::cases();

    expect($cases)->toBeArray()
        ->and($cases)->toHaveCount(3)
        ->and($cases[0])->toBe(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER)
        ->and($cases[1])->toBe(NotifyreRecipientTypes::CONTACT)
        ->and($cases[2])->toBe(NotifyreRecipientTypes::GROUP);
});

test('enum is string backed', function () {
    expect(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER)->toBeInstanceOf(\BackedEnum::class);
});

test('can be used in string comparisons', function () {
    $type = NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER;

    expect($type->value === 'virtual_mobile_number')->toBeTrue()
        ->and($type->value === 'contact')->toBeFalse();
});

test('can be used in switch statements', function () {
    $type = NotifyreRecipientTypes::CONTACT;
    $result = '';

    switch ($type) {
        case NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER:
            $result = 'mobile';
            break;
        case NotifyreRecipientTypes::CONTACT:
            $result = 'contact';
            break;
        case NotifyreRecipientTypes::GROUP:
            $result = 'group';
            break;
    }

    expect($result)->toBe('contact');
});

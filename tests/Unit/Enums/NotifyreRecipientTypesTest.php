<?php

use MagicSystemsIO\Notifyre\Enums\NotifyreRecipientTypes;

it('returns the expected values array', function () {
    expect(NotifyreRecipientTypes::values())->toBe([
        'mobile_number',
        'contact',
        'group',
    ]);
});

it('validates types correctly', function () {
    expect(NotifyreRecipientTypes::isValid('mobile_number'))->toBeTrue()
        ->and(NotifyreRecipientTypes::isValid('contact'))->toBeTrue()
        ->and(NotifyreRecipientTypes::isValid('group'))->toBeTrue()
        ->and(NotifyreRecipientTypes::isValid('not_a_type'))->toBeFalse();
});

it('can create enum instances from values', function () {
    $mobile = NotifyreRecipientTypes::from('mobile_number');
    $contact = NotifyreRecipientTypes::from('contact');
    $group = NotifyreRecipientTypes::from('group');

    expect($mobile)->toBe(NotifyreRecipientTypes::MOBILE_NUMBER)
        ->and($contact)->toBe(NotifyreRecipientTypes::CONTACT)
        ->and($group)->toBe(NotifyreRecipientTypes::GROUP);
});

it('throws when creating from an invalid value', function () {
    expect(fn () => NotifyreRecipientTypes::from('invalid'))->toThrow(ValueError::class);
});

it('cases() and values() are consistent', function () {
    $cases = NotifyreRecipientTypes::cases();
    $caseValues = array_map(fn ($c) => $c->value, $cases);

    expect(count($cases))->toBe(3)
        ->and($caseValues)->toBe(NotifyreRecipientTypes::values());
});

<?php

use MagicSystemsIO\Notifyre\Enums\NotifyreDriver;

it('returns the expected driver values', function () {
    expect(NotifyreDriver::values())->toBe([
        'sms',
        'log',
    ]);
});

it('validates driver values correctly', function () {
    expect(NotifyreDriver::isValid('sms'))->toBeTrue()
        ->and(NotifyreDriver::isValid('log'))->toBeTrue()
        ->and(NotifyreDriver::isValid('invalid'))->toBeFalse();
});

it('can create enum instances from values', function () {
    $sms = NotifyreDriver::from('sms');
    $log = NotifyreDriver::from('log');

    expect($sms)->toBe(NotifyreDriver::SMS)
        ->and($log)->toBe(NotifyreDriver::LOG);
});

it('throws when creating from an invalid value', function () {
    expect(fn () => NotifyreDriver::from('invalid'))->toThrow(ValueError::class);
});

it('cases() and values() are consistent', function () {
    $cases = NotifyreDriver::cases();
    $caseValues = array_map(fn ($c) => $c->value, $cases);

    expect(count($cases))->toBe(2)
        ->and($caseValues)->toBe(NotifyreDriver::values());
});

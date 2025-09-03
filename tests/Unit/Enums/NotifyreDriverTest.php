<?php

namespace MagicSystemsIO\Notifyre\Tests\Unit\Enums;

use BackedEnum;
use MagicSystemsIO\Notifyre\Enums\NotifyreDriver;

test('has correct case values', function () {
    expect(NotifyreDriver::SMS->value)->toBe('sms')
        ->and(NotifyreDriver::LOG->value)->toBe('log');
});

test('values method returns all case values', function () {
    $values = NotifyreDriver::values();

    expect($values)->toBeArray()
        ->and($values)->toHaveCount(2)
        ->and($values)->toContain('sms')
        ->and($values)->toContain('log');
});

test('isValid method returns true for valid drivers', function () {
    expect(NotifyreDriver::isValid('sms'))->toBeTrue()
        ->and(NotifyreDriver::isValid('log'))->toBeTrue();
});

test('isValid method returns false for invalid drivers', function () {
    expect(NotifyreDriver::isValid('invalid_driver'))->toBeFalse()
        ->and(NotifyreDriver::isValid(''))->toBeFalse()
        ->and(NotifyreDriver::isValid('SMS'))->toBeFalse()
        ->and(NotifyreDriver::isValid('Log'))->toBeFalse()
        ->and(NotifyreDriver::isValid('sms_'))->toBeFalse()
        ->and(NotifyreDriver::isValid('email'))->toBeFalse()
        ->and(NotifyreDriver::isValid('push'))->toBeFalse();
});

test('isValid method handles edge cases', function () {
    expect(NotifyreDriver::isValid('   '))->toBeFalse()
        ->and(NotifyreDriver::isValid('sms '))->toBeFalse()
        ->and(NotifyreDriver::isValid(' sms'))->toBeFalse()
        ->and(NotifyreDriver::isValid('sms_'))->toBeFalse()
        ->and(NotifyreDriver::isValid('SMS'))->toBeFalse()
        ->and(NotifyreDriver::isValid('Log'))->toBeFalse();
});

test('cases method returns all enum cases', function () {
    $cases = NotifyreDriver::cases();

    expect($cases)->toBeArray()
        ->and($cases)->toHaveCount(2)
        ->and($cases[0])->toBe(NotifyreDriver::SMS)
        ->and($cases[1])->toBe(NotifyreDriver::LOG);
});

test('enum is string backed', function () {
    expect(NotifyreDriver::SMS)->toBeInstanceOf(BackedEnum::class);
});

test('can be used in string comparisons', function () {
    $driver = NotifyreDriver::SMS;

    expect($driver->value === 'sms')->toBeTrue()
        ->and($driver->value === 'log')->toBeFalse();
});

test('can be used in array keys', function () {
    $driverConfigs = [
        NotifyreDriver::SMS->value => 'sms_config',
        NotifyreDriver::LOG->value => 'log_config',
    ];

    expect($driverConfigs)->toHaveKeys(['sms', 'log'])
        ->and($driverConfigs['sms'])->toBe('sms_config')
        ->and($driverConfigs['log'])->toBe('log_config');
});

test('can be used in method parameters', function () {
    $testFunction = function (NotifyreDriver $driver) {
        return $driver->value;
    };

    expect($testFunction(NotifyreDriver::SMS))->toBe('sms')
        ->and($testFunction(NotifyreDriver::LOG))->toBe('log');
});

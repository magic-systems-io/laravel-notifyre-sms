<?php

use Arbi\Notifyre\Enums\NotifyreDriver;

describe('NotifyreDriver', function () {
    it('has correct SMS driver value', function () {
        expect(NotifyreDriver::SMS->value)->toBe('sms');
    });

    it('has correct LOG driver value', function () {
        expect(NotifyreDriver::LOG->value)->toBe('log');
    });

    it('returns all values correctly', function () {
        $values = NotifyreDriver::values();

        expect($values)->toHaveCount(2);
        expect($values)->toContain('sms');
        expect($values)->toContain('log');
        expect($values)->toBe(['sms', 'log']);
    });

    it('validates correct driver values', function () {
        expect(NotifyreDriver::isValid('sms'))->toBeTrue();
        expect(NotifyreDriver::isValid('log'))->toBeTrue();
    });

    it('rejects invalid driver values', function () {
        expect(NotifyreDriver::isValid('invalid'))->toBeFalse();
        expect(NotifyreDriver::isValid(''))->toBeFalse();
        expect(NotifyreDriver::isValid('SMS'))->toBeFalse();
        expect(NotifyreDriver::isValid('LOG'))->toBeFalse();
    });

    it('has exactly two cases', function () {
        $cases = NotifyreDriver::cases();

        expect($cases)->toHaveCount(2);
        expect($cases)->toContain(NotifyreDriver::SMS);
        expect($cases)->toContain(NotifyreDriver::LOG);
    });

    it('can be used in match statements', function () {
        $result = match (NotifyreDriver::SMS) {
            NotifyreDriver::SMS => 'sms_driver',
            NotifyreDriver::LOG => 'log_driver',
        };

        expect($result)->toBe('sms_driver');
    });

    it('can be compared with string values', function () {
        $driver = NotifyreDriver::LOG;
        expect($driver->value)->toBe('log');
        expect($driver->value === 'log')->toBeTrue();
    });
});

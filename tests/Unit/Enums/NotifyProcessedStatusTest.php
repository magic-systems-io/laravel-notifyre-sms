<?php

use MagicSystemsIO\Notifyre\Enums\NotifyProcessedStatus;

it('returns the expected values array', function () {
    expect(NotifyProcessedStatus::values())->toBe([
        'sent',
        'delivered',
        'queued',
        'failed',
        'pending',
        'undelivered',
    ]);
});

it('validates status values correctly', function () {
    expect(NotifyProcessedStatus::isValid('sent'))->toBeTrue()
        ->and(NotifyProcessedStatus::isValid('delivered'))->toBeTrue()
        ->and(NotifyProcessedStatus::isValid('queued'))->toBeTrue()
        ->and(NotifyProcessedStatus::isValid('failed'))->toBeTrue()
        ->and(NotifyProcessedStatus::isValid('pending'))->toBeTrue()
        ->and(NotifyProcessedStatus::isValid('undelivered'))->toBeTrue()
        ->and(NotifyProcessedStatus::isValid('invalid'))->toBeFalse()
        ->and(NotifyProcessedStatus::isValid('unknown'))->toBeFalse();
});

it('can create enum instances from values', function () {
    $sent = NotifyProcessedStatus::from('sent');
    $delivered = NotifyProcessedStatus::from('delivered');
    $queued = NotifyProcessedStatus::from('queued');
    $failed = NotifyProcessedStatus::from('failed');
    $pending = NotifyProcessedStatus::from('pending');
    $undelivered = NotifyProcessedStatus::from('undelivered');

    expect($sent)->toBe(NotifyProcessedStatus::SENT)
        ->and($delivered)->toBe(NotifyProcessedStatus::DELIVERED)
        ->and($queued)->toBe(NotifyProcessedStatus::QUEUED)
        ->and($failed)->toBe(NotifyProcessedStatus::FAILED)
        ->and($pending)->toBe(NotifyProcessedStatus::PENDING)
        ->and($undelivered)->toBe(NotifyProcessedStatus::UNDELIVERED);
});

it('throws when creating from an invalid value', function () {
    expect(fn () => NotifyProcessedStatus::from('invalid'))->toThrow(ValueError::class);
});

it('cases() and values() are consistent', function () {
    $cases = NotifyProcessedStatus::cases();
    $caseValues = array_map(fn ($c) => $c->value, $cases);

    expect(count($cases))->toBe(6)
        ->and($caseValues)->toBe(NotifyProcessedStatus::values());
});

it('identifies successful statuses correctly', function () {
    expect(NotifyProcessedStatus::SENT->isSuccessful())->toBeTrue()
        ->and(NotifyProcessedStatus::DELIVERED->isSuccessful())->toBeTrue()
        ->and(NotifyProcessedStatus::QUEUED->isSuccessful())->toBeFalse()
        ->and(NotifyProcessedStatus::FAILED->isSuccessful())->toBeFalse()
        ->and(NotifyProcessedStatus::PENDING->isSuccessful())->toBeFalse()
        ->and(NotifyProcessedStatus::UNDELIVERED->isSuccessful())->toBeFalse();
});

it('creates enum from nullable string correctly', function () {
    expect(NotifyProcessedStatus::fromNullableString('sent'))->toBe(NotifyProcessedStatus::SENT)
        ->and(NotifyProcessedStatus::fromNullableString('delivered'))->toBe(NotifyProcessedStatus::DELIVERED)
        ->and(NotifyProcessedStatus::fromNullableString('SENT'))->toBe(NotifyProcessedStatus::SENT)
        ->and(NotifyProcessedStatus::fromNullableString('DELIVERED'))->toBe(NotifyProcessedStatus::DELIVERED)
        ->and(NotifyProcessedStatus::fromNullableString(null))->toBeNull()
        ->and(NotifyProcessedStatus::fromNullableString('invalid'))->toBeNull();
});

it('checks if status string is successful', function () {
    expect(NotifyProcessedStatus::isStatusSuccessful('sent'))->toBeTrue()
        ->and(NotifyProcessedStatus::isStatusSuccessful('delivered'))->toBeTrue()
        ->and(NotifyProcessedStatus::isStatusSuccessful('SENT'))->toBeTrue()
        ->and(NotifyProcessedStatus::isStatusSuccessful('DELIVERED'))->toBeTrue()
        ->and(NotifyProcessedStatus::isStatusSuccessful('queued'))->toBeFalse()
        ->and(NotifyProcessedStatus::isStatusSuccessful('failed'))->toBeFalse()
        ->and(NotifyProcessedStatus::isStatusSuccessful('pending'))->toBeFalse()
        ->and(NotifyProcessedStatus::isStatusSuccessful('undelivered'))->toBeFalse()
        ->and(NotifyProcessedStatus::isStatusSuccessful(null))->toBeFalse()
        ->and(NotifyProcessedStatus::isStatusSuccessful('invalid'))->toBeFalse();
});

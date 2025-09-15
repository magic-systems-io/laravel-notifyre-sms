<?php

use MagicSystemsIO\Notifyre\DTO\SMS\SmsRecipient;

it('can instantiate SmsRecipient DTO with default values', function () {
    $sms = createSmsRecipient();

    expect($sms)->toBeInstanceOf(SmsRecipient::class)
        ->and($sms->id)->toBeString()
        ->and($sms->friendlyID)->toBeString()
        ->and($sms->toNumber)->toBe('+12345678901')
        ->and($sms->fromNumber)->toBe('+09876543210')
        ->and($sms->cost)->toBeFloat()
        ->and($sms->messageParts)->toBeInt()
        ->and($sms->costPerPart)->toBeFloat()
        ->and($sms->status)->toBeString()
        ->and($sms->statusMessage)->toBeString()
        ->and($sms->queuedDateUtc)->toBeInt()
        ->and($sms->completedDateUtc)->toBeInt();
});

it('converts SmsRecipient to array with expected keys', function () {
    $sms = createSmsRecipient([
        'id' => 'sms_test',
        'friendlyID' => 'friendly_test',
        'toNumber' => '+11111111111',
        'fromNumber' => '+22222222222',
        'cost' => 1.23,
        'messageParts' => 2,
        'costPerPart' => 0.615,
        'status' => 'delivered',
        'statusMessage' => 'OK',
        'deliveryStatus' => null,
        'queuedDateUtc' => 1600000000,
        'completedDateUtc' => 1600000060,
    ]);

    $array = $sms->toArray();

    expect($array)->toBeArray()
        ->and($array['id'])->toBe('sms_test')
        ->and($array['friendly_id'])->toBe('friendly_test')
        ->and($array['to_number'])->toBe('+11111111111')
        ->and($array['from_number'])->toBe('+22222222222')
        ->and($array['cost'])->toBe(1.23)
        ->and($array['message_parts'])->toBe(2)
        ->and($array['cost_per_part'])->toBe(0.615)
        ->and($array['status'])->toBe('delivered')
        ->and($array['status_message'])->toBe('OK')
        ->and($array['delivery_status'])->toBeNull()
        ->and($array['queued_date_utc'])->toBe(1600000000)
        ->and($array['completed_date_utc'])->toBe(1600000060);
});

it('allows overriding fields via helper and preserves types', function () {
    $sms = createSmsRecipient([
        'id' => 'override_id',
        'toNumber' => '+19999999999',
        'cost' => 5.5,
        'messageParts' => 3,
        'deliveryStatus' => 'pending',
    ]);

    expect($sms->id)->toBe('override_id')
        ->and($sms->toNumber)->toBe('+19999999999')
        ->and($sms->cost)->toBe(5.5)
        ->and($sms->messageParts)->toBe(3)
        ->and($sms->deliveryStatus)->toBe('pending');
});

it('can create multiple SmsRecipients with helper', function () {
    $list = createSmsRecipients(4);
    expect($list)->toBeArray()
        ->and($list)->toHaveCount(4)
        ->and($list[0])->toBeInstanceOf(SmsRecipient::class);
});

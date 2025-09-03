<?php

namespace MagicSystemsIO\Notifyre\Tests\Unit\DTO\SMS;

use MagicSystemsIO\Notifyre\DTO\SMS\InvalidNumber;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponsePayload;

use function MagicSystemsIO\Notifyre\Tests\Helpers\build_response_payload_long_ids;
use function MagicSystemsIO\Notifyre\Tests\Helpers\build_response_payload_single_invalid_number;
use function MagicSystemsIO\Notifyre\Tests\Helpers\build_response_payload_with_invalid_numbers;
use function MagicSystemsIO\Notifyre\Tests\Helpers\build_success_response_payload;

test('can be instantiated', function () {
    $response_payload = build_success_response_payload();

    expect($response_payload)->toBeInstanceOf(ResponsePayload::class)
        ->and($response_payload->invalidToNumbers)->toBeArray()
        ->and($response_payload->friendlyID)->toBeString()
        ->and($response_payload->smsMessageID)->toBeString();
});

test('toArray method works', function () {
    $response_payload = new ResponsePayload(
        smsMessageID:     'sms-message-id',
        friendlyID:       'friendly-id',
        invalidToNumbers: []
    );

    $array = $response_payload->toArray();

    expect($array)->toBeArray()
        ->and($array)->toHaveKeys(['friendly_id', 'invalid_to_numbers', 'sms_message_id']);
});

test('can be instantiated with invalid numbers', function () {
    $response_payload = build_response_payload_with_invalid_numbers();

    expect($response_payload)->toBeInstanceOf(ResponsePayload::class)
        ->and($response_payload->invalidToNumbers)->toHaveCount(3)
        ->and($response_payload->invalidToNumbers[0])->toBeInstanceOf(InvalidNumber::class);
});

test('can be instantiated with single invalid number', function () {
    $response_payload = build_response_payload_single_invalid_number();

    expect($response_payload)->toBeInstanceOf(ResponsePayload::class)
        ->and($response_payload->invalidToNumbers)->toHaveCount(1)
        ->and($response_payload->invalidToNumbers[0]->number)->toBe('+1234567890');
});

test('can be instantiated with long IDs', function () {
    $response_payload = build_response_payload_long_ids();

    expect($response_payload)->toBeInstanceOf(ResponsePayload::class)
        ->and($response_payload->smsMessageID)->toContain('sms-msg-')
        ->and($response_payload->friendlyID)->toContain('friendly-msg-');
});

test('toArray method works with invalid numbers', function () {
    $response_payload = build_response_payload_with_invalid_numbers();
    $array = $response_payload->toArray();

    expect($array)->toBeArray()
        ->and($array)->toHaveKeys(['friendly_id', 'invalid_to_numbers', 'sms_message_id'])
        ->and($array['invalid_to_numbers'])->toHaveCount(3)
        ->and($array['invalid_to_numbers'][0])->toHaveKeys(['number', 'message']);
});

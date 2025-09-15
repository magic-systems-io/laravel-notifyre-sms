<?php

use MagicSystemsIO\Notifyre\DTO\SMS\ResponsePayload;
use MagicSystemsIO\Notifyre\DTO\SMS\SmsRecipient;

it('can instantiate ResponsePayload DTO with default values', function () {
    $payload = createResponsePayload();

    expect($payload)->toBeInstanceOf(ResponsePayload::class)
        ->and($payload->id)->toBeString()
        ->and($payload->recipients)->toBeArray()
        ->and($payload->recipients[0])->toBeInstanceOf(SmsRecipient::class)
        ->and($payload->invalidToNumbers)->toBeArray();
});

it('can convert ResponsePayload to array including nested recipients and invalid numbers', function () {
    $sms = createSmsRecipient([
        'id' => 'sms_1',
        'toNumber' => '+1111111111',
    ]);

    $invalid = createInvalidNumber([
        'number' => '+9999999999',
        'message' => 'Bad number',
    ]);

    $metadata = createMetadata([
        'requestingUserId' => 'userX',
        'requestingUserEmail' => 'x@example.com',
    ]);

    $payload = createResponsePayload([
        'id' => 'payload_1',
        'friendlyID' => 'friendly_1',
        'accountID' => 'acc_1',
        'createdBy' => 'creator',
        'recipients' => [$sms],
        'status' => 'sent',
        'totalCost' => 2.5,
        'metadata' => $metadata,
        'createdDateUtc' => 1600000000,
        'submittedDateUtc' => 1600000010,
        'completedDateUtc' => 1600000060,
        'lastModifiedDateUtc' => 1600000060,
        'campaignName' => 'Campaign A',
        'invalidToNumbers' => [$invalid],
    ]);

    $array = $payload->toArray();

    expect($array['id'])->toBe('payload_1')
        ->and($array['friendly_id'])->toBe('friendly_1')
        ->and($array['account_id'])->toBe('acc_1')
        ->and($array['created_by'])->toBe('creator')
        ->and($array['recipients'])->toBeArray()
        ->and($array['recipients'][0])->toBe($sms->toArray())
        ->and($array['status'])->toBe('sent')
        ->and($array['total_cost'])->toBe(2.5)
        ->and($array['metadata'])->toBe($metadata->toArray())
        ->and($array['created_date_utc'])->toBe(1600000000)
        ->and($array['invalid_to_numbers'][0])->toBe($invalid->toArray());
});

it('handles empty invalid_to_numbers and maps recipients correctly', function () {
    $smsList = createSmsRecipients(3);
    $payload = createResponsePayload([
        'recipients' => $smsList,
        'invalidToNumbers' => [],
    ]);

    $array = $payload->toArray();
    expect($array['recipients'])->toHaveCount(3)
        ->and($array['invalid_to_numbers'])->toBe([]);
});

it('allows overriding all fields via helper', function () {
    $metadata = createMetadata(['requestingUserId' => 'override_user', 'requestingUserEmail' => 'ov@example.com']);
    $payload = createResponsePayload([
        'id' => 'override_id',
        'friendlyID' => 'override_friendly',
        'accountID' => 'override_acc',
        'createdBy' => 'override_creator',
        'recipients' => createSmsRecipients(2),
        'status' => 'queued',
        'totalCost' => 9.99,
        'metadata' => $metadata,
        'createdDateUtc' => 1,
        'submittedDateUtc' => 2,
        'completedDateUtc' => null,
        'lastModifiedDateUtc' => 3,
        'campaignName' => 'Override Camp',
        'invalidToNumbers' => [createInvalidNumber()],
    ]);

    expect($payload->id)->toBe('override_id')
        ->and($payload->status)->toBe('queued')
        ->and($payload->totalCost)->toBe(9.99)
        ->and($payload->metadata->requestingUserId)->toBe('override_user')
        ->and($payload->recipients)->toHaveCount(2);
});

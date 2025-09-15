<?php

use MagicSystemsIO\Notifyre\DTO\SMS\InvalidNumber;
use MagicSystemsIO\Notifyre\DTO\SMS\Metadata;
use MagicSystemsIO\Notifyre\DTO\SMS\Recipient;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponseBody;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponsePayload;
use MagicSystemsIO\Notifyre\DTO\SMS\SmsRecipient;

pest()->extend(Tests\TestCase::class)
    ->in('Feature');

pest()->extend(Tests\TestCase::class)
    ->in('Unit');

function createRecipient(array $attributes = []): Recipient
{
    return new Recipient(
        type: $attributes['type'] ?? 'mobile_number',
        value: $attributes['value'] ?? '+12345678901',
    );
}

function createRequestBody(array $attributes = []): RequestBody
{
    return new RequestBody(
        body: $attributes['body'] ?? 'Test message',
        recipients: $attributes['recipients'] ?? [createRecipient()],
        sender: $attributes['sender'] ?? null,
        scheduledDate: $attributes['scheduledDate'] ?? null,
        addUnsubscribeLink: $attributes['addUnsubscribeLink'] ?? null,
        callbackUrl: $attributes['callbackUrl'] ?? null,
        metadata: $attributes['metadata'] ?? null,
        campaignName: $attributes['campaignName'] ?? null,
    );
}

function createMetadata(array $attributes = []): Metadata
{
    return new Metadata(
        requestingUserId: $attributes['requestingUserId'] ?? 'user123',
        requestingUserEmail: $attributes['requestingUserEmail'] ?? 'test@example.com',
    );
}

function createSmsRecipient(array $attributes = []): SmsRecipient
{
    return new SmsRecipient(
        id: $attributes['id'] ?? 'sms_123',
        friendlyID: $attributes['friendlyID'] ?? 'friendly_123',
        toNumber: $attributes['toNumber'] ?? '+12345678901',
        fromNumber: $attributes['fromNumber'] ?? '+09876543210',
        cost: $attributes['cost'] ?? 0.05,
        messageParts: $attributes['messageParts'] ?? 1,
        costPerPart: $attributes['costPerPart'] ?? 0.05,
        status: $attributes['status'] ?? 'sent',
        statusMessage: $attributes['statusMessage'] ?? 'Message sent successfully',
        deliveryStatus: $attributes['deliveryStatus'] ?? null,
        queuedDateUtc: $attributes['queuedDateUtc'] ?? time(),
        completedDateUtc: $attributes['completedDateUtc'] ?? time() + 60,
    );
}

function createInvalidNumber(array $attributes = []): InvalidNumber
{
    return new InvalidNumber(
        number: $attributes['number'] ?? '+0000000000',
        message: $attributes['message'] ?? 'Invalid phone number format',
    );
}

function createResponsePayload(array $attributes = []): ResponsePayload
{
    return new ResponsePayload(
        id: $attributes['id'] ?? 'payload_123',
        friendlyID: $attributes['friendlyID'] ?? 'friendly_payload_123',
        accountID: $attributes['accountID'] ?? 'account_123',
        createdBy: $attributes['createdBy'] ?? 'user_123',
        recipients: $attributes['recipients'] ?? [createSmsRecipient()],
        status: $attributes['status'] ?? 'completed',
        totalCost: $attributes['totalCost'] ?? 0.05,
        metadata: $attributes['metadata'] ?? createMetadata(),
        createdDateUtc: $attributes['createdDateUtc'] ?? time(),
        submittedDateUtc: $attributes['submittedDateUtc'] ?? time() + 10,
        completedDateUtc: $attributes['completedDateUtc'] ?? time() + 60,
        lastModifiedDateUtc: $attributes['lastModifiedDateUtc'] ?? time() + 60,
        campaignName: $attributes['campaignName'] ?? 'Test Campaign',
        invalidToNumbers: $attributes['invalidToNumbers'] ?? [],
    );
}

function createResponseBody(array $attributes = []): ResponseBody
{
    return new ResponseBody(
        success: $attributes['success'] ?? true,
        statusCode: $attributes['statusCode'] ?? 200,
        message: $attributes['message'] ?? 'Success',
        payload: $attributes['payload'] ?? createResponsePayload(),
        errors: $attributes['errors'] ?? [],
    );
}

function createRecipients(int $count = 3, array $attributes = []): array
{
    $recipients = [];
    for ($i = 0; $i < $count; $i++) {
        $recipientAttributes = $attributes;
        if (isset($attributes['value'])) {
            $recipientAttributes['value'] = $attributes['value'] . $i;
        }
        $recipients[] = createRecipient($recipientAttributes);
    }

    return $recipients;
}

function createSmsRecipients(int $count = 3, array $attributes = []): array
{
    $recipients = [];
    for ($i = 0; $i < $count; $i++) {
        $recipientAttributes = $attributes;
        if (isset($attributes['id'])) {
            $recipientAttributes['id'] = $attributes['id'] . '_' . $i;
        }
        if (isset($attributes['toNumber'])) {
            $recipientAttributes['toNumber'] = $attributes['toNumber'] . $i;
        }
        $recipients[] = createSmsRecipient($recipientAttributes);
    }

    return $recipients;
}

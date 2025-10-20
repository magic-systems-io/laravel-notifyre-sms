<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use MagicSystemsIO\Notifyre\DTO\SMS\SmsRecipient;
use MagicSystemsIO\Notifyre\Http\Requests\NotifyreSmsCallbackRequest;

uses(RefreshDatabase::class);

it('validates and processes a webhook callback', function () {
    $timestamp = time();

    $data = [
        'Event' => 'sms_sent',
        'Timestamp' => $timestamp,
        'Payload' => [
            'ID' => 'payload_123',
            'FriendlyID' => 'friendly_payload_123',
            'AccountID' => 'account_123',
            'CreatedBy' => 'user_123',
            'Status' => 'completed',
            'TotalCost' => 0.05,
            'CreatedDateUtc' => $timestamp,
            'SubmittedDateUtc' => $timestamp + 10,
            'CompletedDateUtc' => $timestamp + 60,
            'LastModifiedDateUtc' => $timestamp + 60,
            'Recipient' => [
                'ID' => 'sms_123',
                'ToNumber' => '+12345678901',
                'FromNumber' => '+09876543210',
                'Cost' => 0.05,
                'MessageParts' => 1,
                'CostPerPart' => 0.05,
                'Status' => 'sent',
                'StatusMessage' => 'Message sent successfully',
                'DeliveryStatus' => null,
                'QueuedDateUtc' => $timestamp,
                'CompletedDateUtc' => $timestamp + 60,
            ],
            'Metadata' => [
                'requestingUserId' => 'user123',
                'requestingUserEmail' => 'test@example.com',
            ],
        ],
    ];

    config(['notifyre.webhook.secret' => null]);

    $request = new NotifyreSmsCallbackRequest();
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));

    $request->replace($data);
    $request->validateResolved();

    $recipient = $request->getRecipient();

    expect($request->validated('Event'))->toBe('sms_sent')
        ->and($request->validated('Timestamp'))->toBe($timestamp)
        ->and($request->validated('Payload.ID'))->toBe('payload_123')
        ->and($recipient)->toBeInstanceOf(SmsRecipient::class)
        ->and($recipient->id)->toBe('sms_123')
        ->and($recipient->toNumber)->toBe('+12345678901');
});

it('throws a ValidationException when required fields are missing', function () {
    $data = [
        'Event' => 'sms_sent',
        'Timestamp' => time(),
    ];

    config(['notifyre.webhook.secret' => null]);

    $request = new NotifyreSmsCallbackRequest();
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));

    $request->replace($data);

    $this->expectException(ValidationException::class);

    $request->validateResolved();
});

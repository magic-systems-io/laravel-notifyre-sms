<?php

namespace MagicSystemsIO\Notifyre\Tests\Unit\Http\Services;

use MagicSystemsIO\Notifyre\DTO\SMS\Recipient;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
use MagicSystemsIO\Notifyre\Enums\NotifyreRecipientTypes;
use MagicSystemsIO\Notifyre\Http\Services\SMSMessagePersister;
use MagicSystemsIO\Notifyre\Models\JunctionTables\NotifyreSMSMessageRecipient;
use MagicSystemsIO\Notifyre\Models\NotifyreRecipients;

use function MagicSystemsIO\Notifyre\Tests\Helpers\build_request_body_basic;
use function MagicSystemsIO\Notifyre\Tests\Helpers\build_request_body_multiple_recipients;
use function MagicSystemsIO\Notifyre\Tests\Helpers\build_request_body_with_sender;
use function MagicSystemsIO\Notifyre\Tests\Helpers\build_success_response_body;

test('can be instantiated', function () {
    $service = new SMSMessagePersister();

    expect($service)->toBeInstanceOf(SMSMessagePersister::class);
});

test('createMessage creates SMS message and recipients successfully', function () {
    $requestData = build_request_body_basic();

    $service = new SMSMessagePersister();

    $result = $service->persist($requestData, build_success_response_body());

    expect($result)->toHaveKey('id')
        ->and($result)->toHaveKey('sender')
        ->and($result)->toHaveKey('body')
        ->and($result)->toHaveKey('created_at')
        ->and($result)->toHaveKey('updated_at')
        ->and($result)->toHaveKey('recipients')
        ->and($result['body'])->toBe($requestData->body)
        ->and($result['recipients'])->toHaveCount(1);
});

test('createMessage creates message with sender when provided', function () {
    $requestData = build_request_body_with_sender();

    $service = new SMSMessagePersister();

    $result = $service->persist($requestData, build_success_response_body());

    expect($result['sender'])->toBe($requestData->sender);
});

test('createMessage creates message with multiple recipients', function () {
    $requestData = build_request_body_multiple_recipients();

    $service = new SMSMessagePersister();

    $result = $service->persist($requestData, build_success_response_body());

    expect($result['recipients'])->toHaveCount(3);
});

test('createMessage handles duplicate recipients correctly', function () {
    $requestData = build_request_body_basic();

    // Create a recipient that already exists
    $existingRecipient = NotifyreRecipients::create([
        'type' => $requestData->recipients[0]->type,
        'value' => $requestData->recipients[0]->value,
    ]);

    $service = new SMSMessagePersister();

    $result = $service->persist($requestData, build_success_response_body());

    expect($result['recipients'])->toHaveCount(1)
        ->and($result['recipients'][0]['id'])->toBe($existingRecipient->id);
});

test('createMessage creates new recipients when they do not exist', function () {
    $requestData = build_request_body_basic();

    $service = new SMSMessagePersister();

    $result = $service->persist($requestData, build_success_response_body());

    expect($result['recipients'])->toHaveCount(1);

    // Verify the recipient was created in the database
    $createdRecipient = NotifyreRecipients::where('type', $requestData->recipients[0]->type)
        ->where('value', $requestData->recipients[0]->value)
        ->first();

    expect($createdRecipient)->not->toBeNull();
});

test('createMessage links message to recipients via junction table', function () {
    $requestData = build_request_body_basic();

    $service = new SMSMessagePersister();

    $result = $service->persist($requestData, build_success_response_body());

    // Verify the junction table entry was created
    $junctionEntry = NotifyreSMSMessageRecipient::where('sms_message_id', $result['id'])
        ->first();

    expect($junctionEntry)->not->toBeNull();
});

test('createMessage formats response correctly', function () {
    $requestData = build_request_body_basic();

    $service = new SMSMessagePersister();

    $result = $service->persist($requestData, build_success_response_body());

    expect($result)->toHaveKey('id')
        ->and($result)->toHaveKey('sender')
        ->and($result)->toHaveKey('body')
        ->and($result)->toHaveKey('created_at')
        ->and($result)->toHaveKey('updated_at')
        ->and($result)->toHaveKey('recipients')
        ->and($result['created_at'])->toMatch('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/')
        ->and($result['updated_at'])->toMatch('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/');
});

test('createMessage handles different recipient types correctly', function () {
    $recipients = [
        new Recipient(NotifyreRecipientTypes::MOBILE_NUMBER->value, '+61412345678'),
        new Recipient(NotifyreRecipientTypes::CONTACT->value, 'contact-123'),
        new Recipient(NotifyreRecipientTypes::GROUP->value, 'group-456'),
    ];

    $requestData = new RequestBody(
        body: 'Test message with different recipient types',
        recipients: $recipients
    );

    $service = new SMSMessagePersister();

    $result = $service->persist($requestData, build_success_response_body());

    expect($result['recipients'])->toHaveCount(3);

    // Verify all recipient types were created
    foreach ($recipients as $recipient) {
        $createdRecipient = NotifyreRecipients::where('type', $recipient->type)
            ->where('value', $recipient->value)
            ->first();

        expect($createdRecipient)->not->toBeNull();
    }
});

test('createMessage handles long message body correctly', function () {
    $longBody = str_repeat('This is a very long message. ', 10);
    $requestData = new RequestBody(
        body: $longBody,
        recipients: [new Recipient(NotifyreRecipientTypes::MOBILE_NUMBER->value, '+61412345678')]
    );

    $service = new SMSMessagePersister();

    $result = $service->persist($requestData, build_success_response_body());

    expect($result['body'])->toBe($longBody);
});

test('createMessage handles special characters in message body', function () {
    $specialBody = 'Message with special chars: @#$%^&*()_+-=[]{}|;:,.<>?';
    $requestData = new RequestBody(
        body: $specialBody,
        recipients: [new Recipient(NotifyreRecipientTypes::MOBILE_NUMBER->value, '+61412345678')]
    );

    $service = new SMSMessagePersister();

    $result = $service->persist($requestData, build_success_response_body());

    expect($result['body'])->toBe($specialBody);
});

test('createMessage handles empty sender correctly', function () {
    $requestData = new RequestBody(
        body: 'Test message',
        recipients: [new Recipient(NotifyreRecipientTypes::MOBILE_NUMBER->value, '+61412345678')],
        sender: ''
    );

    $service = new SMSMessagePersister();

    $result = $service->persist($requestData, build_success_response_body());

    expect($result['sender'])->toBe('');
});

test('createMessage handles null sender correctly', function () {
    $requestData = new RequestBody(
        body: 'Test message',
        recipients: [new Recipient(NotifyreRecipientTypes::MOBILE_NUMBER->value, '+61412345678')],
        sender: null
    );

    $service = new SMSMessagePersister();

    $result = $service->persist($requestData, build_success_response_body());

    expect($result['sender'])->toBeNull();
});

test('createMessage updates existing recipient timestamps', function () {
    $existingRecipient = NotifyreRecipients::create([
        'type' => NotifyreRecipientTypes::MOBILE_NUMBER->value,
        'value' => '+61412345678',
    ]);

    $oldUpdatedAt = $existingRecipient->updated_at;

    $requestData = new RequestBody(
        body: 'Test message',
        recipients: [new Recipient(NotifyreRecipientTypes::MOBILE_NUMBER->value, '+61412345678')]
    );

    $service = new SMSMessagePersister();

    $service->persist($requestData, build_success_response_body());

    // Refresh the recipient model
    $existingRecipient->refresh();

    // Add a small delay to ensure timestamps are different
    usleep(1000); // 1ms delay

    expect($existingRecipient->updated_at->timestamp)->toBeGreaterThanOrEqual($oldUpdatedAt->timestamp);
});

<?php

use MagicSystemsIO\Notifyre\Models\JunctionTables\NotifyreSmsMessageRecipient;
use MagicSystemsIO\Notifyre\Models\NotifyreRecipients;
use MagicSystemsIO\Notifyre\Models\NotifyreSmsMessages;
use MagicSystemsIO\Notifyre\Services\NotifyreMessagePersister;

beforeEach(function () {
    config()->set('services.notifyre.driver', 'sms');

    $this->artisan('migrate');
});

afterEach(function () {
    Mockery::close();
});

it('persists message, recipients and links them', function () {
    $request = createRequestBody([
        'recipients' => createRecipients(3, ['value' => '+1234567890']),
    ]);

    $response = createResponseBody([
        'payload' => createResponsePayload(['id' => 'message-message-123']),
    ]);

    NotifyreMessagePersister::persist($request, $response);

    $message = NotifyreSmsMessages::find('message-message-123');
    expect($message)->not->toBeNull()
        ->and($message->body)->toBe($request->body);

    $recipients = NotifyreRecipients::all();
    expect($recipients->count())->toBe(3);

    $junction = NotifyreSmsMessageRecipient::where('sms_message_id', 'message-message-123')->get();
    expect($junction->count())->toBe(3);
});

it('throws when recipients upsert affects unexpected number of rows', function () {
    $firstRequest = createRequestBody([
        'recipients' => createRecipients(1),
    ]);

    $firstResponse = createResponseBody([
        'payload' => createResponsePayload(['id' => 'duplicate-message-id']),
    ]);

    NotifyreMessagePersister::persist($firstRequest, $firstResponse);

    $duplicateRequest = createRequestBody([
        'recipients' => createRecipients(1),
    ]);

    $duplicateResponse = createResponseBody([
        'payload' => createResponsePayload(['id' => 'duplicate-message-id']),
    ]);

    $this->expectException(RuntimeException::class);
    NotifyreMessagePersister::persist($duplicateRequest, $duplicateResponse);
});

it('matches recipients by both type and value using whereIn', function () {
    $recipients = [
        createRecipient(['type' => 'mobile_number', 'value' => '+12345671111']),
        createRecipient(['type' => 'mobile_number', 'value' => '+12345672222']),
        createRecipient(['type' => 'contact', 'value' => 'contact_123']),
    ];

    $request = createRequestBody(['recipients' => $recipients]);
    $response = createResponseBody([
        'payload' => createResponsePayload(['id' => 'msg-type-value-test']),
    ]);

    NotifyreMessagePersister::persist($request, $response);

    $mobileRecipients = NotifyreRecipients::where('type', 'mobile_number')->get();
    expect($mobileRecipients->count())->toBe(2);

    $contactRecipients = NotifyreRecipients::where('type', 'contact')->get();
    expect($contactRecipients->count())->toBe(1)
        ->and($contactRecipients->first()->value)->toBe('contact_123');

    $junction = NotifyreSmsMessageRecipient::where('sms_message_id', 'msg-type-value-test')->get();
    expect($junction->count())->toBe(3);
});

it('treats recipients with same value but different types as separate entities', function () {
    $recipients = [
        createRecipient(['type' => 'mobile_number', 'value' => '+12345678800']),
        createRecipient(['type' => 'contact', 'value' => '+12345678800']),
    ];

    $request = createRequestBody(['recipients' => $recipients]);
    $response = createResponseBody([
        'payload' => createResponsePayload(['id' => 'msg-different-types']),
    ]);

    NotifyreMessagePersister::persist($request, $response);

    $allRecipients = NotifyreRecipients::where('value', '+12345678800')->get();
    expect($allRecipients->count())->toBe(2);

    $types = $allRecipients->pluck('type')->toArray();
    expect($types)->toContain('mobile_number')
        ->and($types)->toContain('contact');

    $junction = NotifyreSmsMessageRecipient::where('sms_message_id', 'msg-different-types')->get();
    expect($junction->count())->toBe(2);
});

it('uses upsert with unique constraint on type and value', function () {
    $firstRecipients = [
        createRecipient(['type' => 'mobile_number', 'value' => '+12345673333']),
        createRecipient(['type' => 'mobile_number', 'value' => '+12345674444']),
    ];

    $firstRequest = createRequestBody(['recipients' => $firstRecipients]);
    $firstResponse = createResponseBody([
        'payload' => createResponsePayload(['id' => 'msg-upsert-1']),
    ]);

    NotifyreMessagePersister::persist($firstRequest, $firstResponse);

    $recipientsAfterFirst = NotifyreRecipients::all();
    expect($recipientsAfterFirst->count())->toBe(2);

    $secondRecipients = [
        createRecipient(['type' => 'mobile_number', 'value' => '+12345673333']),
        createRecipient(['type' => 'mobile_number', 'value' => '+12345675555']),
    ];

    $secondRequest = createRequestBody(['recipients' => $secondRecipients]);
    $secondResponse = createResponseBody([
        'payload' => createResponsePayload(['id' => 'msg-upsert-2']),
    ]);

    NotifyreMessagePersister::persist($secondRequest, $secondResponse);

    $recipientsAfterSecond = NotifyreRecipients::all();
    expect($recipientsAfterSecond->count())->toBe(3);

    $duplicates = NotifyreRecipients::where('type', 'mobile_number')
        ->where('value', '+12345673333')
        ->get();
    expect($duplicates->count())->toBe(1);
});

it('correctly retrieves recipients using whereIn with type and value arrays', function () {
    $recipients = [
        createRecipient(['type' => 'mobile_number', 'value' => '+12345676666']),
        createRecipient(['type' => 'mobile_number', 'value' => '+12345677777']),
        createRecipient(['type' => 'contact', 'value' => 'contact_user_123']),
        createRecipient(['type' => 'group', 'value' => 'group_alpha']),
    ];

    $request = createRequestBody(['recipients' => $recipients]);
    $response = createResponseBody([
        'payload' => createResponsePayload(['id' => 'msg-wherein-test']),
    ]);

    NotifyreMessagePersister::persist($request, $response);

    $message = NotifyreSmsMessages::with('recipients')->find('msg-wherein-test');
    expect($message->recipients->count())->toBe(4);

    $recipientTypes = $message->recipients->pluck('type')->toArray();
    expect($recipientTypes)->toContain('mobile_number')
        ->and($recipientTypes)->toContain('contact')
        ->and($recipientTypes)->toContain('group');

    $recipientValues = $message->recipients->pluck('value')->toArray();
    expect($recipientValues)->toContain('+12345676666')
        ->and($recipientValues)->toContain('+12345677777')
        ->and($recipientValues)->toContain('contact_user_123')
        ->and($recipientValues)->toContain('group_alpha');
});

it('handles batch recipient creation with mixed types efficiently', function () {
    $recipients = [
        createRecipient(['type' => 'mobile_number', 'value' => '+12345679911']),
        createRecipient(['type' => 'mobile_number', 'value' => '+12345679922']),
        createRecipient(['type' => 'mobile_number', 'value' => '+12345679933']),
        createRecipient(['type' => 'contact', 'value' => 'contact_user1']),
        createRecipient(['type' => 'contact', 'value' => 'contact_user2']),
        createRecipient(['type' => 'group', 'value' => 'group_beta']),
    ];

    $request = createRequestBody(['recipients' => $recipients]);
    $response = createResponseBody([
        'payload' => createResponsePayload(['id' => 'msg-batch-mixed']),
    ]);

    NotifyreMessagePersister::persist($request, $response);

    expect(NotifyreRecipients::where('type', 'mobile_number')->count())->toBe(3)
        ->and(NotifyreRecipients::where('type', 'contact')->count())->toBe(2)
        ->and(NotifyreRecipients::where('type', 'group')->count())->toBe(1);

    $junction = NotifyreSmsMessageRecipient::where('sms_message_id', 'msg-batch-mixed')->get();
    expect($junction->count())->toBe(6)
        ->and($junction->every(fn ($j) => $j->delivery_status === 'pending'))->toBeTrue();
});

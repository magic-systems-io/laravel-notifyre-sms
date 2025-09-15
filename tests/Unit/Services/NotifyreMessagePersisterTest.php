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

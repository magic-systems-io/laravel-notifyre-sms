<?php

use MagicSystemsIO\Notifyre\Contracts\NotifyreManager;
use MagicSystemsIO\Notifyre\DTO\SMS\InvalidNumber;
use MagicSystemsIO\Notifyre\DTO\SMS\Metadata;
use MagicSystemsIO\Notifyre\DTO\SMS\Recipient;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponseBody;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponsePayload;
use MagicSystemsIO\Notifyre\DTO\SMS\SmsRecipient;

beforeEach(function () {
    $this->mock = Mockery::mock(NotifyreManager::class);
    app()->instance(NotifyreManager::class, $this->mock);
});

it('has notifyre helper functions', function () {
    expect(notifyre())->toBe($this->mock);
});

it('createRecipient returns a Recipient DTO with defaults', function () {
    $recipient = createRecipient();

    expect($recipient)->toBeInstanceOf(Recipient::class)
        ->and($recipient->type)->toBe('mobile_number')
        ->and($recipient->value)->toBe('+12345678901');
});

it('createRequestBody returns a RequestBody with recipients and default body', function () {
    $body = createRequestBody();

    expect($body)->toBeInstanceOf(RequestBody::class)
        ->and($body->body)->toBe('Test message')
        ->and($body->recipients)->toBeArray()
        ->and($body->recipients)->toHaveCount(1)
        ->and($body->recipients[0])->toBeInstanceOf(Recipient::class);
});

it('createResponseBody returns a ResponseBody with payload and defaults', function () {
    $response = createResponseBody();

    expect($response)->toBeInstanceOf(ResponseBody::class)
        ->and($response->success)->toBeTrue()
        ->and($response->statusCode)->toBe(200)
        ->and($response->payload)->toBeInstanceOf(ResponsePayload::class);
});

it('createResponsePayload and createSmsRecipient return proper DTOs', function () {
    $payload = createResponsePayload();
    $sms = createSmsRecipient();

    expect($payload)->toBeInstanceOf(ResponsePayload::class)
        ->and($payload->recipients)->toBeArray()
        ->and($payload->recipients[0])->toBeInstanceOf(SmsRecipient::class)
        ->and($sms)->toBeInstanceOf(SmsRecipient::class)
        ->and($sms->toNumber)->toBe('+12345678901');
});

it('createRecipients and createSmsRecipients produce multiple items', function () {
    $recipients = createRecipients(2);
    $smsRecipients = createSmsRecipients(2);

    expect($recipients)->toBeArray()
        ->and($recipients)->toHaveCount(2)
        ->and($recipients[0])->toBeInstanceOf(Recipient::class)
        ->and($smsRecipients)->toBeArray()
        ->and($smsRecipients)->toHaveCount(2)
        ->and($smsRecipients[0])->toBeInstanceOf(SmsRecipient::class);
});

it('createMetadata and createInvalidNumber return expected DTOs', function () {
    $meta = createMetadata();
    $invalid = createInvalidNumber();

    expect($meta)->toBeInstanceOf(Metadata::class)
        ->and($meta->requestingUserId)->toBe('user123')
        ->and($meta->requestingUserEmail)->toBe('test@example.com')
        ->and($invalid)->toBeInstanceOf(InvalidNumber::class)
        ->and($invalid->number)->toBe('+0000000000');
});

afterEach(function () {
    Mockery::close();
});

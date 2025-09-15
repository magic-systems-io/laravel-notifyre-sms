<?php

use MagicSystemsIO\Notifyre\DTO\SMS\Recipient;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;

it('can instantiate RequestBody DTO with default values', function () {
    $body = createRequestBody();
    expect($body)->toBeInstanceOf(RequestBody::class)
        ->and($body->body)->toBe('Test message')
        ->and($body->recipients)->toBeArray()
        ->and($body->recipients[0])->toBeInstanceOf(Recipient::class);
});

it('can instantiate RequestBody DTO with custom values', function () {
    $recipients = createRecipients(2);
    $metadata = createMetadata(['requestingUserId' => 'custom', 'requestingUserEmail' => 'custom@example.com']);
    $body = createRequestBody([
        'body' => 'Custom message',
        'recipients' => $recipients,
        'sender' => 'SenderName',
        'scheduledDate' => 1234567890,
        'addUnsubscribeLink' => true,
        'callbackUrl' => 'https://callback.url',
        'metadata' => $metadata->toArray(),
        'campaignName' => 'My Campaign',
    ]);
    expect($body->body)->toBe('Custom message')
        ->and($body->recipients)->toHaveCount(2)
        ->and($body->sender)->toBe('SenderName')
        ->and($body->scheduledDate)->toBe(1234567890)
        ->and($body->addUnsubscribeLink)->toBeTrue()
        ->and($body->callbackUrl)->toBe('https://callback.url')
        ->and($body->metadata)->toBe($metadata->toArray())
        ->and($body->campaignName)->toBe('My Campaign');
});

it('can convert RequestBody DTO to array', function () {
    $recipients = createRecipients(2);
    $body = createRequestBody([
        'body' => 'Array test',
        'recipients' => $recipients,
        'sender' => 'Sender',
        'addUnsubscribeLink' => false,
    ]);
    $array = $body->toArray();
    expect($array['Body'])->toBe('Array test')
        ->and($array['Recipients'])->toBeArray()
        ->and($array['Recipients'][0])->toBe($recipients[0]->toArray())
        ->and($array['From'])->toBe('Sender')
        ->and($array['AddUnsubscribeLink'])->toBeFalse()
        ->and($array)->not->toHaveKey('ScheduledDate');
});

it('throws exception for empty body', function () {
    expect(fn () => createRequestBody(['body' => '']))->toThrow(InvalidArgumentException::class)
        ->and(fn () => createRequestBody(['body' => '   ']))->toThrow(InvalidArgumentException::class);
});

it('throws exception for empty recipients', function () {
    expect(fn () => createRequestBody(['recipients' => []]))->toThrow(InvalidArgumentException::class);
});

it('can handle multiple recipients', function () {
    $recipients = createRecipients(5);
    $body = createRequestBody(['recipients' => $recipients]);
    expect($body->recipients)->toHaveCount(5);
    $array = $body->toArray();
    expect($array['Recipients'])->toHaveCount(5);
});

it('can include metadata as array', function () {
    $metadata = createMetadata(['requestingUserId' => 'meta', 'requestingUserEmail' => 'meta@example.com']);
    $body = createRequestBody(['metadata' => $metadata->toArray()]);
    $array = $body->toArray();
    expect($array['Metadata'])->toBe($metadata->toArray());
});

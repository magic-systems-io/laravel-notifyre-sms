<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use MagicSystemsIO\Notifyre\Enums\NotifyreRecipientTypes;
use MagicSystemsIO\Notifyre\Http\Requests\NotifyreSmsMessagesRequest;

uses(RefreshDatabase::class);

it('validates a well-formed request', function () {
    $data = [
        'body' => 'Hello world',
        'sender' => 'TestSender',
        'recipients' => [
            ['type' => NotifyreRecipientTypes::MOBILE_NUMBER->value, 'value' => '+1234567890'],
        ],
        'persist' => true,
    ];

    $request = new NotifyreSmsMessagesRequest();
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));

    $request->replace($data);
    // should not throw
    $request->validateResolved();

    $validated = $request->validated();

    expect($validated['body'])->toBe('Hello world')
        ->and($validated['sender'])->toBe('TestSender')
        ->and(is_array($validated['recipients']))->toBeTrue()
        ->and($validated['recipients'][0]['type'])->toBe(NotifyreRecipientTypes::MOBILE_NUMBER->value);
});

it('fails when body is missing', function () {
    $data = [
        'recipients' => [['type' => NotifyreRecipientTypes::MOBILE_NUMBER->value, 'value' => '+123']],
    ];

    $request = new NotifyreSmsMessagesRequest();
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));
    $request->replace($data);

    $this->expectException(ValidationException::class);

    $request->validateResolved();
});

it('fails when body exceeds max length', function () {
    $data = [
        'body' => str_repeat('a', 161),
        'recipients' => [['type' => NotifyreRecipientTypes::MOBILE_NUMBER->value, 'value' => '+123']],
    ];

    $request = new NotifyreSmsMessagesRequest();
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));
    $request->replace($data);

    $this->expectException(ValidationException::class);

    $request->validateResolved();
});

it('fails when recipient type is invalid', function () {
    $data = [
        'body' => 'Hi',
        'recipients' => [['type' => 'invalid_type', 'value' => '+123']],
    ];

    $request = new NotifyreSmsMessagesRequest();
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));
    $request->replace($data);

    $this->expectException(ValidationException::class);

    $request->validateResolved();
});

it('fails when recipients array is empty', function () {
    $data = [
        'body' => 'Hello',
        'recipients' => [],
    ];

    $request = new NotifyreSmsMessagesRequest();
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));
    $request->replace($data);

    $this->expectException(ValidationException::class);

    $request->validateResolved();
});

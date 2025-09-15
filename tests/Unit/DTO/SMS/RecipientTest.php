<?php

use MagicSystemsIO\Notifyre\DTO\SMS\Recipient;

it('can instantiate Recipient DTO with default values', function () {
    $recipient = createRecipient();
    expect($recipient)->toBeInstanceOf(Recipient::class)
        ->and($recipient->type)->toBe('mobile_number')
        ->and($recipient->value)->toBe('+12345678901');
});

it('can instantiate Recipient DTO with custom values', function () {
    $recipient = createRecipient([
        'type' => 'contact',
        'value' => '123123123',
    ]);
    expect($recipient->type)->toBe('contact')
        ->and($recipient->value)->toBe('123123123');
});

it('can convert Recipient DTO to array', function () {
    $recipient = createRecipient([
        'type' => 'mobile_number',
        'value' => '+12345678901',
    ]);
    expect($recipient->toArray())->toBe([
        'type' => 'mobile_number',
        'value' => '+12345678901',
    ]);
});

it('normalizes country code if missing and config is set', function () {
    config(['notifyre.default_number_prefix' => '+44']);
    $recipient = createRecipient([
        'value' => '07123456789',
    ]);
    expect($recipient->value)->toBe('+447123456789');
});

it('throws exception if country code missing and config not set', function () {
    config(['notifyre.default_number_prefix' => null]);
    expect(fn () => createRecipient(['value' => '07123456789']))->toThrow(InvalidArgumentException::class);
});

it('throws exception for invalid recipient value', function () {
    expect(fn () => createRecipient(['value' => 'invalid_value']))->toThrow(InvalidArgumentException::class);
});

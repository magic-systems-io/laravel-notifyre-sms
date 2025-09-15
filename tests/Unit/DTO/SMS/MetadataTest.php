<?php

use MagicSystemsIO\Notifyre\DTO\SMS\Metadata;

it('can instantiate Metadata DTO and access properties', function () {
    $dto = new Metadata(
        requestingUserId: 'user123',
        requestingUserEmail: 'test@example.com',
    );

    expect($dto)->toBeInstanceOf(Metadata::class)
        ->and($dto->requestingUserId)->toBe('user123')
        ->and($dto->requestingUserEmail)->toBe('test@example.com');
});

it('can convert Metadata DTO to array', function () {
    $dto = new Metadata(
        requestingUserId: 'user456',
        requestingUserEmail: 'other@example.com',
    );

    expect($dto->toArray())->toBe([
        'requesting_user_id' => 'user456',
        'requesting_user_email' => 'other@example.com',
    ]);
});

it('createMetadata helper returns default values and accepts overrides', function () {
    $default = createMetadata();
    expect($default)->toBeInstanceOf(Metadata::class)
        ->and($default->requestingUserId)->toBe('user123')
        ->and($default->requestingUserEmail)->toBe('test@example.com');

    $custom = createMetadata([
        'requestingUserId' => 'custom_id',
        'requestingUserEmail' => 'custom@example.com',
    ]);
    expect($custom->requestingUserId)->toBe('custom_id')
        ->and($custom->requestingUserEmail)->toBe('custom@example.com');
});

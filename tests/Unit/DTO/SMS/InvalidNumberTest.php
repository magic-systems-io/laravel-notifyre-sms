<?php

use MagicSystemsIO\Notifyre\DTO\SMS\InvalidNumber;

it('can instantiate InvalidNumber DTO and convert to array', function () {
    $dto = new InvalidNumber(number: '+0000000000', message: 'Invalid phone number format');

    expect($dto)->toBeInstanceOf(InvalidNumber::class)
        ->and($dto->number)->toBe('+0000000000')
        ->and($dto->message)->toBe('Invalid phone number format')
        ->and($dto->toArray())->toBe([
            'number' => '+0000000000',
            'message' => 'Invalid phone number format',
        ]);
});

it('createInvalidNumber helper returns default values and accepts overrides', function () {
    $default = createInvalidNumber();

    expect($default)->toBeInstanceOf(InvalidNumber::class)
        ->and($default->number)->toBe('+0000000000')
        ->and($default->message)->toBe('Invalid phone number format');

    $custom = createInvalidNumber(['number' => '+1111111111', 'message' => 'Custom message']);
    expect($custom->number)->toBe('+1111111111')
        ->and($custom->message)->toBe('Custom message');
});

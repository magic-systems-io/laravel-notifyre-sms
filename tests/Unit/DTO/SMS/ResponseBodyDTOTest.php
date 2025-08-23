<?php

namespace MagicSystemsIO\Notifyre\Tests\Unit\DTO\SMS;

use MagicSystemsIO\Notifyre\DTO\SMS\ResponseBodyDTO;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponsePayload;

describe('ResponseBodyDTO', function () {
    it('creates a valid ResponseBodyDTO with all parameters', function () {
        $payload = new ResponsePayload(
            smsMessageID: 'msg_123',
            friendlyID: 'friendly_456',
            invalidToNumbers: []
        );

        $dto = new ResponseBodyDTO(
            success: true,
            statusCode: 200,
            message: 'SMS sent successfully',
            payload: $payload,
            errors: []
        );

        expect($dto->success)->toBeTrue()
            ->and($dto->statusCode)->toBe(200)
            ->and($dto->message)->toBe('SMS sent successfully')
            ->and($dto->payload)->toBe($payload)
            ->and($dto->errors)->toBe([]);
    });

    it('creates a ResponseBodyDTO with failure response', function () {
        $payload = new ResponsePayload(
            smsMessageID: '',
            friendlyID: '',
            invalidToNumbers: ['+1234567890']
        );

        $dto = new ResponseBodyDTO(
            success: false,
            statusCode: 400,
            message: 'Invalid phone number',
            payload: $payload,
            errors: ['Invalid recipient']
        );

        expect($dto->success)->toBeFalse()
            ->and($dto->statusCode)->toBe(400)
            ->and($dto->message)->toBe('Invalid phone number')
            ->and($dto->payload)->toBe($payload)
            ->and($dto->errors)->toBe(['Invalid recipient']);
    });

    it('creates a ResponseBodyDTO with minimal parameters', function () {
        $payload = new ResponsePayload(
            smsMessageID: '',
            friendlyID: '',
            invalidToNumbers: []
        );

        $dto = new ResponseBodyDTO(
            success: false,
            statusCode: 500,
            message: '',
            payload: $payload,
            errors: []
        );

        expect($dto->success)->toBeFalse()
            ->and($dto->statusCode)->toBe(500)
            ->and($dto->message)->toBe('')
            ->and($dto->payload)->toBe($payload)
            ->and($dto->errors)->toBe([]);
    });


    it('is readonly', function () {
        $dto = new ResponseBodyDTO(
            success: true,
            statusCode: 200,
            message: 'Test',
            payload: new ResponsePayload('msg_123', 'friendly_456', []),
            errors: []
        );

        expect($dto->toArray())->toBeArray();
    });
});

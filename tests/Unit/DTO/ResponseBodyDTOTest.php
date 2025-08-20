<?php

use Arbi\Notifyre\DTO\SMS\ResponseBodyDTO;
use Arbi\Notifyre\DTO\SMS\ResponsePayload;

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

        expect($dto->success)->toBeTrue();
        expect($dto->statusCode)->toBe(200);
        expect($dto->message)->toBe('SMS sent successfully');
        expect($dto->payload)->toBe($payload);
        expect($dto->errors)->toBe([]);
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

        expect($dto->success)->toBeFalse();
        expect($dto->statusCode)->toBe(400);
        expect($dto->message)->toBe('Invalid phone number');
        expect($dto->payload)->toBe($payload);
        expect($dto->errors)->toBe(['Invalid recipient']);
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

        expect($dto->success)->toBeFalse();
        expect($dto->statusCode)->toBe(500);
        expect($dto->message)->toBe('');
        expect($dto->payload)->toBe($payload);
        expect($dto->errors)->toBe([]);
    });
});

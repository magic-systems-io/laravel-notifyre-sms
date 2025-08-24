<?php

namespace MagicSystemsIO\Notifyre\Tests\Unit\DTO\SMS;

use MagicSystemsIO\Notifyre\DTO\SMS\ResponsePayload;

describe('ResponsePayload', function () {
    it('creates a valid ResponsePayload with all parameters', function () {
        $payload = new ResponsePayload(
            smsMessageID: 'msg_123456',
            friendlyID: 'friendly_789',
            invalidToNumbers: []
        );

        expect($payload->smsMessageID)->toBe('msg_123456')
            ->and($payload->friendlyID)->toBe('friendly_789')
            ->and($payload->invalidToNumbers)->toBe([]);
    });

    it('creates a ResponsePayload with invalid numbers', function () {
        $payload = new ResponsePayload(
            smsMessageID: 'msg_123',
            friendlyID: 'friendly_456',
            invalidToNumbers: ['+1234567890', '+0987654321']
        );

        expect($payload->smsMessageID)->toBe('msg_123')
            ->and($payload->friendlyID)->toBe('friendly_456')
            ->and($payload->invalidToNumbers)->toBe(['+1234567890', '+0987654321'])
            ->and($payload->invalidToNumbers)->toHaveCount(2);
    });

    it('creates a ResponsePayload with empty values', function () {
        $payload = new ResponsePayload(
            smsMessageID: '',
            friendlyID: '',
            invalidToNumbers: []
        );

        expect($payload->smsMessageID)->toBe('')
            ->and($payload->friendlyID)->toBe('')
            ->and($payload->invalidToNumbers)->toBe([]);
    });

    it('creates a ResponsePayload with long IDs', function () {
        $longMessageID = str_repeat('msg_', 100) . '123';
        $longFriendlyID = str_repeat('friendly_', 100) . '456';

        $payload = new ResponsePayload(
            smsMessageID: $longMessageID,
            friendlyID: $longFriendlyID,
            invalidToNumbers: []
        );

        expect($payload->smsMessageID)->toBe($longMessageID)
            ->and($payload->friendlyID)->toBe($longFriendlyID);
    });

    it('creates a ResponsePayload with special characters in IDs', function () {
        $payload = new ResponsePayload(
            smsMessageID: 'msg_123-456_789',
            friendlyID: 'friendly@456#789',
            invalidToNumbers: []
        );

        expect($payload->smsMessageID)->toBe('msg_123-456_789')
            ->and($payload->friendlyID)->toBe('friendly@456#789');
    });


    it('is readonly', function () {
        $payload = new ResponsePayload(
            smsMessageID: '18a0c628-6a5d-4a6b-816c-3962b7ce5e33',
            friendlyID: 'df863968-2e31-4138-94c5-f225156659ec',
            invalidToNumbers: []
        );

        expect($payload->toArray())->toBeArray();
    });
});

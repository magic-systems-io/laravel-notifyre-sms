<?php

namespace Arbi\Notifyre\Tests\Unit\DTO\SMS;

use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Error;
use InvalidArgumentException;

describe('RequestBodyDTO', function () {
    it('creates a valid RequestBodyDTO with all parameters', function () {
        $recipients = [
            new Recipient('mobile_number', '+1234567890'),
        ];

        $dto = new RequestBodyDTO(
            body: 'Test message',
            sender: 'TestApp',
            recipients: $recipients
        );

        expect($dto->body)->toBe('Test message')
            ->and($dto->sender)->toBe('TestApp')
            ->and($dto->recipients)->toBe($recipients);
    });

    it('creates a valid RequestBodyDTO without sender', function () {
        $recipients = [
            new Recipient('mobile_number', '+1234567890'),
        ];

        $dto = new RequestBodyDTO(
            body: 'Test message',
            sender: null,
            recipients: $recipients
        );

        expect($dto->body)->toBe('Test message')
            ->and($dto->sender)->toBeNull()
            ->and($dto->recipients)->toBe($recipients);
    });

    it('throws exception when body is empty', function () {
        $recipients = [
            new Recipient('mobile_number', '+1234567890'),
        ];

        expect(fn () => new RequestBodyDTO(
            body: '',
            sender: 'TestApp',
            recipients: $recipients
        ))->toThrow(InvalidArgumentException::class, 'Body cannot be empty');
    });

    it('throws exception when body is whitespace only', function () {
        $recipients = [
            new Recipient('mobile_number', '+1234567890'),
        ];

        expect(fn () => new RequestBodyDTO(
            body: '   ',
            sender: 'TestApp',
            recipients: $recipients
        ))->toThrow(InvalidArgumentException::class, 'Body cannot be empty');
    });

    it('throws exception when recipients array is empty', function () {
        expect(fn () => new RequestBodyDTO(
            body: 'Test message',
            sender: 'TestApp',
            recipients: []
        ))->toThrow(InvalidArgumentException::class, 'Recipients cannot be empty');
    });

    it('accepts body with special characters', function () {
        $recipients = [
            new Recipient('mobile_number', '+1234567890'),
        ];

        $specialBody = 'Hello! This is a test message with special chars: @#$%^&*()_+-=[]{}|;:,.<>?';

        $dto = new RequestBodyDTO(
            body: $specialBody,
            sender: 'TestApp',
            recipients: $recipients
        );

        expect($dto->body)->toBe($specialBody);
    });

    it('accepts very long body', function () {
        $recipients = [
            new Recipient('mobile_number', '+1234567890'),
        ];

        $longBody = str_repeat('This is a very long message. ', 100);

        $dto = new RequestBodyDTO(
            body: $longBody,
            sender: 'TestApp',
            recipients: $recipients
        );

        expect($dto->body)->toBe($longBody);
    });

    it('accepts multiple recipients', function () {
        $recipients = [
            new Recipient('mobile_number', '+1234567890'),
            new Recipient('mobile_number', '+0987654321'),
            new Recipient('contact', 'contact123'),
        ];

        $dto = new RequestBodyDTO(
            body: 'Test message',
            sender: 'TestApp',
            recipients: $recipients
        );

        expect($dto->recipients)->toHaveCount(3)
            ->and($dto->recipients)->toBe($recipients);
    });

    it('is readonly', function () {
        $dto = new RequestBodyDTO(
            body: 'Test message',
            sender: 'TestApp',
            recipients: [
                new Recipient('mobile_number', '+1234567890'),
            ]
        );

        expect(fn () => $dto->body = 'Other')->toThrow(Error::class);
    });

});

<?php

namespace Arbi\Notifyre\Tests\Unit\DTO\SMS;

use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Arbi\Notifyre\Enums\NotifyreRecipientTypes;
use InvalidArgumentException;

describe('RequestBodyDTO', function () {
    it('creates a valid request body DTO', function () {
        $recipients = [
            new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, '+1234567890'),
        ];

        $requestBody = new RequestBodyDTO('Test message', $recipients);

        expect($requestBody->body)->toBe('Test message')
            ->and($requestBody->recipients)->toBe($recipients);
    });

    it('creates a valid request body DTO with sender', function () {
        $recipients = [
            new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, '+1234567890'),
        ];

        $requestBody = new RequestBodyDTO('Test message', $recipients, '+1987654321');

        expect($requestBody->body)->toBe('Test message')
            ->and($requestBody->recipients)->toBe($recipients)
            ->and($requestBody->from)->toBe('+1987654321');
    });

    it('creates a valid request body DTO with all optional parameters', function () {
        $recipients = [
            new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, '+1234567890'),
            new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, '+0987654321'),
        ];

        $requestBody = new RequestBodyDTO(
            'Test message',
            $recipients,
            '+1987654321',
            1234567890,
            true,
            'https://example.com/callback',
            ['key1' => 'value1', 'key2' => 'value2'],
            'Test Campaign'
        );

        expect($requestBody->body)->toBe('Test message')
            ->and($requestBody->recipients)->toBe($recipients)
            ->and($requestBody->from)->toBe('+1987654321')
            ->and($requestBody->scheduledDate)->toBe(1234567890)
            ->and($requestBody->addUnsubscribeLink)->toBe(true)
            ->and($requestBody->callbackUrl)->toBe('https://example.com/callback')
            ->and($requestBody->metadata)->toBe(['key1' => 'value1', 'key2' => 'value2'])
            ->and($requestBody->campaignName)->toBe('Test Campaign');
    });

    it('throws exception for empty body', function () {
        $recipients = [
            new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, '+1234567890'),
        ];

        expect(fn () => new RequestBodyDTO('', $recipients))
            ->toThrow(InvalidArgumentException::class, 'Body cannot be empty');
    });

    it('throws exception for whitespace-only body', function () {
        $recipients = [
            new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, '+1234567890'),
        ];

        expect(fn () => new RequestBodyDTO('   ', $recipients))
            ->toThrow(InvalidArgumentException::class, 'Body cannot be empty');
    });

    it('throws exception for empty recipients', function () {
        expect(fn () => new RequestBodyDTO('Test message', []))
            ->toThrow(InvalidArgumentException::class, 'Recipients cannot be empty');
    });

    it('throws exception for metadata with too many keys', function () {
        $recipients = [
            new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, '+1234567890'),
        ];

        $metadata = [];
        for ($i = 1; $i <= 51; $i++) {
            $metadata["key{$i}"] = "value{$i}";
        }

        expect(fn () => new RequestBodyDTO('Test message', $recipients, null, null, false, null, $metadata))
            ->toThrow(InvalidArgumentException::class, 'Metadata cannot exceed 50 keys');
    });

    it('throws exception for metadata key too long', function () {
        $recipients = [
            new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, '+1234567890'),
        ];

        $longKey = str_repeat('a', 51);
        $metadata = [$longKey => 'value'];

        expect(fn () => new RequestBodyDTO('Test message', $recipients, null, null, false, null, $metadata))
            ->toThrow(InvalidArgumentException::class, 'Metadata key cannot exceed 50 characters');
    });

    it('throws exception for metadata value too long', function () {
        $recipients = [
            new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, '+1234567890'),
        ];

        $longValue = str_repeat('a', 501);
        $metadata = ['key' => $longValue];

        expect(fn () => new RequestBodyDTO('Test message', $recipients, null, null, false, null, $metadata))
            ->toThrow(InvalidArgumentException::class, 'Metadata value cannot exceed 500 characters');
    });

    it('converts to array correctly', function () {
        $recipients = [
            new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, '+1234567890'),
        ];

        $requestBody = new RequestBodyDTO('Test message', $recipients);

        $array = $requestBody->toArray();

        expect($array)->toHaveKey('Body')
            ->and($array)->toHaveKey('Recipients')
            ->and($array['Body'])->toBe('Test message')
            ->and($array['Recipients'])->toBeArray();
    });

    it('converts to array with all optional parameters', function () {
        $recipients = [
            new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, '+1234567890'),
        ];

        $requestBody = new RequestBodyDTO(
            'Test message',
            $recipients,
            '+1987654321',
            1234567890,
            true,
            'https://example.com/callback',
            ['key1' => 'value1'],
            'Test Campaign'
        );

        $array = $requestBody->toArray();

        expect($array)->toHaveKey('Body')
            ->and($array)->toHaveKey('Recipients')
            ->and($array)->toHaveKey('From')
            ->and($array)->toHaveKey('ScheduledDate')
            ->and($array)->toHaveKey('AddUnsubscribeLink')
            ->and($array)->toHaveKey('CallbackUrl')
            ->and($array)->toHaveKey('Metadata')
            ->and($array)->toHaveKey('CampaignName')
            ->and($array['Body'])->toBe('Test message')
            ->and($array['From'])->toBe('+1987654321')
            ->and($array['ScheduledDate'])->toBe(1234567890)
            ->and($array['AddUnsubscribeLink'])->toBe(true)
            ->and($array['CallbackUrl'])->toBe('https://example.com/callback')
            ->and($array['Metadata'])->toBe(['key1' => 'value1'])
            ->and($array['CampaignName'])->toBe('Test Campaign');
    });
});

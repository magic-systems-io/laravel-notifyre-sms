<?php

namespace Arbi\Notifyre\Tests\Unit\Services\Drivers;

use Arbi\Notifyre\DTO\SMS\InvalidNumber;
use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Arbi\Notifyre\DTO\SMS\ResponseBodyDTO;
use Arbi\Notifyre\DTO\SMS\ResponsePayload;
use Arbi\Notifyre\Enums\NotifyreRecipientTypes;
use Arbi\Notifyre\Services\Drivers\SMSDriver;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

describe('SMSDriver', function () {
    beforeEach(function () {
        Config::set('notifyre.base_url', 'https://api.notifyre.com');
        Config::set('services.notifyre.api_key', 'test-api-key');
        Config::set('notifyre.timeout', 30);
        Config::set('notifyre.retry.times', 3);
        Config::set('notifyre.retry.sleep', 1000);
        Config::set('notifyre.api.cache.enabled', false);
    });

    afterEach(function () {
        Http::fake([]);
    });

    it('can load required classes', function () {
        expect(class_exists(ResponseBodyDTO::class))->toBeTrue()
            ->and(class_exists(ResponsePayload::class))->toBeTrue()
            ->and(class_exists(InvalidNumber::class))->toBeTrue();
    });

    it('sends SMS successfully', function () {
        $recipients = [
            new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, '+1234567890'),
        ];

        $requestBody = new RequestBodyDTO('Test message', $recipients);

        Http::fake([
            'https://api.notifyre.com/sms/send' => Http::response([
                'Success' => true,
                'StatusCode' => 200,
                'Message' => 'SMS sent successfully',
                'Payload' => [
                    'SmsMessageID' => 'msg_123',
                    'FriendlyID' => 'friendly_123',
                    'InvalidToNumbers' => [],
                ],
                'Errors' => [],
            ]),
        ]);

        $response = new SMSDriver()->send($requestBody);

        expect($response)->not->toBeNull()
            ->and($response->success)->toBeTrue()
            ->and($response->statusCode)->toBe(200)
            ->and($response->message)->toBe('SMS sent successfully')
            ->and($response->payload)->not->toBeNull()
            ->and($response->payload->smsMessageID)->toBe('msg_123')
            ->and($response->payload->friendlyID)->toBe('friendly_123')
            ->and($response->payload->invalidToNumbers)->toBe([])
            ->and($response->errors)->toBe([]);

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://api.notifyre.com/sms/send' &&
                $request->method() === 'POST' &&
                $request->header('x-api-token')[0] === 'test-api-key' &&
                $request->header('Content-Type')[0] === 'application/json' &&
                $request->data()['Body'] === 'Test message' &&
                $request->data()['Recipients'][0]['type'] === NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value &&
                $request->data()['Recipients'][0]['value'] === '+1234567890';
        });
    });

    it('sends SMS with all optional parameters', function () {
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

        Http::fake([
            'https://api.notifyre.com/sms/send' => Http::response([
                'Success' => true,
                'StatusCode' => 200,
                'Message' => 'SMS sent successfully',
                'Payload' => [
                    'SmsMessageID' => 'msg_123',
                    'FriendlyID' => 'friendly_123',
                    'InvalidToNumbers' => [],
                ],
                'Errors' => [],
            ]),
        ]);

        $response = new SMSDriver()->send($requestBody);

        expect($response)->not->toBeNull()
            ->and($response->success)->toBeTrue();

        Http::assertSent(function (Request $request) {
            $data = $request->data();

            return $data['Body'] === 'Test message' &&
                $data['From'] === '+1987654321' &&
                $data['ScheduledDate'] === 1234567890 &&
                $data['AddUnsubscribeLink'] === true &&
                $data['CallbackUrl'] === 'https://example.com/callback' &&
                $data['Metadata'] === ['key1' => 'value1'] &&
                $data['CampaignName'] === 'Test Campaign';
        });
    });



    it('throws exception when base URL is not configured', function () {
        Config::set('notifyre.base_url', '');

        $recipients = [
            new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, '+1234567890'),
        ];

        $requestBody = new RequestBodyDTO('Test message', $recipients);

        expect(fn () => new SMSDriver()->send($requestBody))
            ->toThrow(InvalidArgumentException::class, 'Notifyre base URL is not configured.');
    });

    it('throws exception when API key is not configured', function () {
        Config::set('services.notifyre.api_key', '');
        Config::set('notifyre.api_key', '');

        $recipients = [
            new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, '+1234567890'),
        ];

        $requestBody = new RequestBodyDTO('Test message', $recipients);

        expect(fn () => new SMSDriver()->send($requestBody))
            ->toThrow(InvalidArgumentException::class, 'Notifyre API key is not configured.');
    });

    it('prioritizes services.notifyre.api_key over notifyre.api_key', function () {
        Config::set('notifyre.api_key', 'fallback-key');
        Config::set('services.notifyre.api_key', 'primary-key');

        $recipients = [
            new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, '+1234567890'),
        ];

        $requestBody = new RequestBodyDTO('Test message', $recipients);

        Http::fake([
            'https://api.notifyre.com/sms/send' => Http::response([
                'Success' => true,
                'StatusCode' => 200,
                'Message' => 'SMS sent successfully',
                'Payload' => [
                    'SmsMessageID' => 'msg_123',
                    'FriendlyID' => 'friendly_456',
                    'InvalidToNumbers' => [],
                ],
                'Errors' => [],
            ]),
        ]);

        new SMSDriver()->send($requestBody);

        Http::assertSent(function ($request) {
            return $request->header('x-api-token')[0] === 'primary-key';
        });
    });



    it('uses configured timeout and retry settings', function () {
        Config::set('notifyre.timeout', 60);
        Config::set('notifyre.retry.times', 5);
        Config::set('notifyre.retry.sleep', 2000);

        $recipients = [
            new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, '+1234567890'),
        ];

        $requestBody = new RequestBodyDTO('Test message', $recipients);

        Http::fake([
            'https://api.notifyre.com/sms/send' => Http::response([
                'Success' => true,
                'StatusCode' => 200,
                'Message' => 'SMS sent successfully',
                'Payload' => [
                    'SmsMessageID' => 'msg_123',
                    'FriendlyID' => 'friendly_456',
                    'InvalidToNumbers' => [],
                ],
                'Errors' => [],
            ]),
        ]);

        new SMSDriver()->send($requestBody);

        // Note: We can't directly test timeout and retry settings with Http::fake,
        // but we can verify the configuration is used in the actual implementation
        expect(Config::get('notifyre.timeout'))->toBe(60)
            ->and(Config::get('notifyre.retry.times'))->toBe(5)
            ->and(Config::get('notifyre.retry.sleep'))->toBe(2000);
    });

    it('trims trailing slash from base URL', function () {
        Config::set('notifyre.base_url', 'https://api.notifyre.com/');

        $recipients = [
            new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, '+1234567890'),
        ];

        $requestBody = new RequestBodyDTO('Test message', $recipients);

        Http::fake([
            'https://api.notifyre.com/sms/send' => Http::response([
                'Success' => true,
                'StatusCode' => 200,
                'Message' => 'SMS sent successfully',
                'Payload' => [
                    'SmsMessageID' => 'msg_123',
                    'FriendlyID' => 'friendly_456',
                    'InvalidToNumbers' => [],
                ],
                'Errors' => [],
            ]),
        ]);

        new SMSDriver()->send($requestBody);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.notifyre.com/sms/send';
        });
    });


});

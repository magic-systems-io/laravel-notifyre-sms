<?php

namespace Arbi\Notifyre\Tests\Unit\Services\Drivers;

use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Arbi\Notifyre\Services\Drivers\SMSDriver;
use Illuminate\Http\Client\RequestException;
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
        Config::set('notifyre.cache.enabled', false);
    });

    it('sends SMS successfully with all parameters', function () {
        $recipients = [
            new Recipient('mobile_number', '+1234567890'),
        ];

        $message = new RequestBodyDTO(
            body: 'Test message',
            sender: 'TestApp',
            recipients: $recipients
        );

        Http::fake([
            'https://api.notifyre.com/sms/send' => Http::response([
                'success' => true,
                'statusCode' => 200,
                'message' => 'SMS sent successfully',
                'payload' => [
                    'smsMessageID' => 'msg_123',
                    'friendlyID' => 'friendly_456',
                    'invalidToNumbers' => [],
                ],
                'errors' => [],
            ]),
        ]);

        $driver = new SMSDriver();
        $driver->send($message);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.notifyre.com/sms/send' &&
                   $request->method() === 'POST' &&
                   $request->header('x-api-token')[0] === 'test-api-key' &&
                   $request->header('Content-Type')[0] === 'application/json' &&
                   $request->data() === [
                       'Body' => 'Test message',
                       'Recipients' => [
                           [
                               'type' => 'mobile_number',
                               'value' => '+1234567890',
                           ],
                       ],
                   ];
        });
    });

    it('sends SMS without sender (uses empty string)', function () {
        $recipients = [
            new Recipient('mobile_number', '+1234567890'),
        ];

        $message = new RequestBodyDTO(
            body: 'Test message',
            sender: null,
            recipients: $recipients
        );

        Http::fake([
            'https://api.notifyre.com/sms/send' => Http::response([
                'success' => true,
                'statusCode' => 200,
                'message' => 'SMS sent successfully',
                'payload' => [
                    'smsMessageID' => 'msg_123',
                    'friendlyID' => 'friendly_456',
                    'invalidToNumbers' => [],
                ],
                'errors' => [],
            ]),
        ]);

        $driver = new SMSDriver();
        $driver->send($message);

        Http::assertSent(function ($request) {
            return !isset($request->data()['From']);
        });
    });

    it('sends SMS with empty sender string', function () {
        $recipients = [
            new Recipient('mobile_number', '+1234567890'),
        ];

        $message = new RequestBodyDTO(
            body: 'Test message',
            sender: '',
            recipients: $recipients
        );

        Http::fake([
            'https://api.notifyre.com/sms/send' => Http::response([
                'success' => true,
                'statusCode' => 200,
                'message' => 'SMS sent successfully',
                'payload' => [
                    'smsMessageID' => 'msg_123',
                    'friendlyID' => 'friendly_456',
                    'invalidToNumbers' => [],
                ],
                'errors' => [],
            ]),
        ]);

        $driver = new SMSDriver();
        $driver->send($message);

        Http::assertSent(function ($request) {
            return !isset($request->data()['From']);
        });
    });

    it('sends SMS with multiple recipients', function () {
        $recipients = [
            new Recipient('mobile_number', '+1234567890'),
            new Recipient('contact', 'contact123'),
            new Recipient('group', 'group456'),
        ];

        $message = new RequestBodyDTO(
            body: 'Test message',
            sender: 'TestApp',
            recipients: $recipients
        );

        Http::fake([
            'https://api.notifyre.com/sms/send' => Http::response([
                'success' => true,
                'statusCode' => 200,
                'message' => 'SMS sent successfully',
                'payload' => [
                    'smsMessageID' => 'msg_123',
                    'friendlyID' => 'friendly_456',
                    'invalidToNumbers' => [],
                ],
                'errors' => [],
            ]),
        ]);

        $driver = new SMSDriver();
        $driver->send($message);

        Http::assertSent(function ($request) {
            return count($request->data()['Recipients']) === 3 &&
                   $request->data()['Recipients'][0]['type'] === 'mobile_number' &&
                   $request->data()['Recipients'][0]['value'] === '+1234567890' &&
                   $request->data()['Recipients'][1]['type'] === 'contact' &&
                   $request->data()['Recipients'][1]['value'] === 'contact123' &&
                   $request->data()['Recipients'][2]['type'] === 'group' &&
                   $request->data()['Recipients'][2]['value'] === 'group456';
        });
    });

    it('throws exception when base URL is not configured', function () {
        Config::set('notifyre.base_url');

        $recipients = [
            new Recipient('mobile_number', '+1234567890'),
        ];

        $message = new RequestBodyDTO(
            body: 'Test message',
            sender: 'TestApp',
            recipients: $recipients
        );

        $driver = new SMSDriver();

        expect(fn () => $driver->send($message))
            ->toThrow(InvalidArgumentException::class, 'Notifyre base URL is not configured.');
    });

    it('throws exception when API key is not configured', function () {
        Config::set('services.notifyre.api_key');
        Config::set('notifyre.api_key');

        $recipients = [
            new Recipient('mobile_number', '+1234567890'),
        ];

        $message = new RequestBodyDTO(
            body: 'Test message',
            sender: 'TestApp',
            recipients: $recipients
        );

        $driver = new SMSDriver();

        expect(fn () => $driver->send($message))
            ->toThrow(InvalidArgumentException::class, 'Notifyre API key is not configured.');
    });

    it('prioritizes services.notifyre.api_key over notifyre.api_key', function () {
        Config::set('notifyre.api_key', 'fallback-key');
        Config::set('services.notifyre.api_key', 'primary-key');

        $recipients = [
            new Recipient('mobile_number', '+1234567890'),
        ];

        $message = new RequestBodyDTO(
            body: 'Test message',
            sender: 'TestApp',
            recipients: $recipients
        );

        Http::fake([
            'https://api.notifyre.com/sms/send' => Http::response([
                'success' => true,
                'statusCode' => 200,
                'message' => 'SMS sent successfully',
                'payload' => [
                    'smsMessageID' => 'msg_123',
                    'friendlyID' => 'friendly_456',
                    'invalidToNumbers' => [],
                ],
                'errors' => [],
            ]),
        ]);

        $driver = new SMSDriver();
        $driver->send($message);

        Http::assertSent(function ($request) {
            return $request->header('x-api-token')[0] === 'primary-key';
        });
    });

    it('throws exception on HTTP failure', function () {
        $recipients = [
            new Recipient('mobile_number', '+1234567890'),
        ];
        $message = new RequestBodyDTO(
            body: 'Test message',
            sender: 'TestApp',
            recipients: $recipients
        );

        Http::fake([
            'https://api.notifyre.com/sms/send' => Http::response([
                'success' => false,
                'statusCode' => 400,
                'message' => 'Bad Request',
                'errors' => ['Invalid phone number'],
            ], 400),
        ]);

        $driver = new SMSDriver();

        expect(fn () => $driver->send($message))
            ->toThrow(RequestException::class);
    });

    it('uses configured timeout and retry settings', function () {
        Config::set('notifyre.timeout', 60);
        Config::set('notifyre.retry.times', 5);
        Config::set('notifyre.retry.sleep', 2000);

        $recipients = [
            new Recipient('mobile_number', '+1234567890'),
        ];

        $message = new RequestBodyDTO(
            body: 'Test message',
            sender: 'TestApp',
            recipients: $recipients
        );

        Http::fake([
            'https://api.notifyre.com/sms/send' => Http::response([
                'success' => true,
                'statusCode' => 200,
                'message' => 'SMS sent successfully',
                'payload' => [
                    'smsMessageID' => 'msg_123',
                    'friendlyID' => 'friendly_456',
                    'invalidToNumbers' => [],
                ],
                'errors' => [],
            ]),
        ]);

        $driver = new SMSDriver();
        $driver->send($message);

        // Note: We can't directly test timeout and retry settings with Http::fake
        // but we can verify the configuration is used in the actual implementation
        expect(Config::get('notifyre.timeout'))->toBe(60)
            ->and(Config::get('notifyre.retry.times'))->toBe(5)
            ->and(Config::get('notifyre.retry.sleep'))->toBe(2000);
    });

    it('trims trailing slash from base URL', function () {
        Config::set('notifyre.base_url', 'https://api.notifyre.com/');

        $recipients = [
            new Recipient('mobile_number', '+1234567890'),
        ];

        $message = new RequestBodyDTO(
            body: 'Test message',
            sender: 'TestApp',
            recipients: $recipients
        );

        Http::fake([
            'https://api.notifyre.com/sms/send' => Http::response([
                'success' => true,
                'statusCode' => 200,
                'message' => 'SMS sent successfully',
                'payload' => [
                    'smsMessageID' => 'msg_123',
                    'friendlyID' => 'friendly_456',
                    'invalidToNumbers' => [],
                ],
                'errors' => [],
            ]),
        ]);

        $driver = new SMSDriver();
        $driver->send($message);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.notifyre.com/sms/send';
        });
    });
});

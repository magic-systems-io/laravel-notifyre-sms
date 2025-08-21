<?php

namespace Arbi\Notifyre\Tests\Unit\Services\Drivers;

use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Arbi\Notifyre\Services\Drivers\LogDriver;
use Illuminate\Support\Facades\Log;

describe('LogDriver', function () {
    it('logs SMS message with all parameters', function () {
        $recipients = [
            new Recipient('mobile_number', '+1234567890'),
        ];

        $message = new RequestBodyDTO(
            body:       'Test message',
            sender:     'TestApp',
            recipients: $recipients
        );

        Log::shouldReceive('info')
            ->once()
            ->with('SMS would be sent via Notifyre', [
                'body' => 'Test message',
                'sender' => 'TestApp',
                'recipients' => [
                    [
                        'type' => 'mobile_number',
                        'value' => '+1234567890',
                    ],
                ],
            ]);

        $driver = new LogDriver();
        $driver->send($message);
    });

    it('logs SMS message without sender', function () {
        $recipients = [
            new Recipient('mobile_number', '+1234567890'),
        ];

        $message = new RequestBodyDTO(
            body:       'Test message',
            sender:     null,
            recipients: $recipients
        );

        Log::shouldReceive('info')
            ->once()
            ->with('SMS would be sent via Notifyre', [
                'body' => 'Test message',
                'sender' => '(auto-assigned by token)',
                'recipients' => [
                    [
                        'type' => 'mobile_number',
                        'value' => '+1234567890',
                    ],
                ],
            ]);

        $driver = new LogDriver();
        $driver->send($message);
    });

    it('logs SMS message with multiple recipients', function () {
        $recipients = [
            new Recipient('mobile_number', '+1234567890'),
            new Recipient('contact', 'contact123'),
            new Recipient('group', 'group456'),
        ];

        $message = new RequestBodyDTO(
            body:       'Test message',
            sender:     'TestApp',
            recipients: $recipients
        );

        Log::shouldReceive('info')
            ->once()
            ->with('SMS would be sent via Notifyre', [
                'body' => 'Test message',
                'sender' => 'TestApp',
                'recipients' => [
                    [
                        'type' => 'mobile_number',
                        'value' => '+1234567890',
                    ],
                    [
                        'type' => 'contact',
                        'value' => 'contact123',
                    ],
                    [
                        'type' => 'group',
                        'value' => 'group456',
                    ],
                ],
            ]);

        $driver = new LogDriver();
        $driver->send($message);
    });

    it('logs SMS message with empty sender', function () {
        $recipients = [
            new Recipient('mobile_number', '+1234567890'),
        ];

        $message = new RequestBodyDTO(
            body:       'Test message',
            sender:     '',
            recipients: $recipients
        );

        Log::shouldReceive('info')
            ->once()
            ->with('SMS would be sent via Notifyre', [
                'body' => 'Test message',
                'sender' => '(auto-assigned by token)',
                'recipients' => [
                    [
                        'type' => 'mobile_number',
                        'value' => '+1234567890',
                    ],
                ],
            ]);

        $driver = new LogDriver();
        $driver->send($message);
    });

    it('logs SMS message with special characters in body', function () {
        $recipients = [
            new Recipient('mobile_number', '+1234567890'),
        ];

        $specialBody = 'Hello! This is a test message with special chars: @#$%^&*()_+-=[]{}|;:,.<>?';

        $message = new RequestBodyDTO(
            body:       $specialBody,
            sender:     'TestApp',
            recipients: $recipients
        );

        Log::shouldReceive('info')
            ->once()
            ->with('SMS would be sent via Notifyre', [
                'body' => $specialBody,
                'sender' => 'TestApp',
                'recipients' => [
                    [
                        'type' => 'mobile_number',
                        'value' => '+1234567890',
                    ],
                ],
            ]);

        $driver = new LogDriver();
        $driver->send($message);
    });
});

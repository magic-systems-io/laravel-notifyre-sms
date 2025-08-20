<?php

use Arbi\Notifyre\Commands\NotifyreSmsSendCommand;
use Arbi\Notifyre\Contracts\NotifyreServiceInterface;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

describe('NotifyreSmsSendCommand', function () {
    beforeEach(function () {
        Config::set('notifyre.default_sender', 'DefaultApp');
        Config::set('notifyre.default_recipient', '+1234567890');
    });

    it('sends SMS with all arguments provided', function () {
        $mockService = Mockery::mock(NotifyreServiceInterface::class);
        $mockService->shouldReceive('send')
            ->once()
            ->with(Mockery::on(function (RequestBodyDTO $dto) {
                return $dto->body === 'Test message' &&
                       $dto->sender === 'TestApp' &&
                       count($dto->recipients) === 1 &&
                       $dto->recipients[0]->type === 'mobile_number' &&
                       $dto->recipients[0]->value === '+0987654321';
            }));

        $command = new NotifyreSmsSendCommand($mockService);

        $command->setLaravel(app());
        $command->setInput(new ArrayInput([
            'sender' => 'TestApp',
            'recipient' => '+0987654321',
            'message' => 'Test message',
        ]));

        $command->run(
            new ArrayInput([
                'sender' => 'TestApp',
                'recipient' => '+0987654321',
                'message' => 'Test message',
            ]),
            new BufferedOutput()
        );

        Mockery::close();
    });

    it('uses default sender when only recipient and message provided', function () {
        $mockService = Mockery::mock(NotifyreServiceInterface::class);
        $mockService->shouldReceive('send')
            ->once()
            ->with(Mockery::on(function (RequestBodyDTO $dto) {
                return $dto->sender === 'DefaultApp';
            }));

        $command = new NotifyreSmsSendCommand($mockService);

        $command->setLaravel(app());
        $command->setInput(new ArrayInput([
            'recipient' => '+0987654321',
            'message' => 'Test message',
        ]));

        $command->run(
            new ArrayInput([
                'recipient' => '+0987654321',
                'message' => 'Test message',
            ]),
            new BufferedOutput()
        );

        Mockery::close();
    });

    it('uses default recipient when only sender and message provided', function () {
        $mockService = Mockery::mock(NotifyreServiceInterface::class);
        $mockService->shouldReceive('send')
            ->once()
            ->with(Mockery::on(function (RequestBodyDTO $dto) {
                return $dto->recipients[0]->value === '+1234567890';
            }));

        $command = new NotifyreSmsSendCommand($mockService);

        $command->setLaravel(app());
        $command->setInput(new ArrayInput([
            'sender' => 'TestApp',
            'message' => 'Test message',
        ]));

        $command->run(
            new ArrayInput([
                'sender' => 'TestApp',
                'message' => 'Test message',
            ]),
            new BufferedOutput()
        );

        Mockery::close();
    });

    it('uses both defaults when only message provided', function () {
        $mockService = Mockery::mock(NotifyreServiceInterface::class);
        $mockService->shouldReceive('send')
            ->once()
            ->with(Mockery::on(function (RequestBodyDTO $dto) {
                return $dto->sender === 'DefaultApp' &&
                       $dto->recipients[0]->value === '+1234567890';
            }));

        $command = new NotifyreSmsSendCommand($mockService);

        $command->setLaravel(app());
        $command->setInput(new ArrayInput([
            'message' => 'Test message',
        ]));

        $command->run(
            new ArrayInput([
                'message' => 'Test message',
            ]),
            new BufferedOutput()
        );

        Mockery::close();
    });

    it('returns early when no message provided', function () {
        $mockService = Mockery::mock(NotifyreServiceInterface::class);
        $mockService->shouldNotReceive('send');

        $command = new NotifyreSmsSendCommand($mockService);

        $command->setLaravel(app());
        $command->setInput(new ArrayInput([
            'sender' => 'TestApp',
            'recipient' => '+0987654321',
        ]));

        $command->run(
            new ArrayInput([
                'sender' => 'TestApp',
                'recipient' => '+0987654321',
            ]),
            new BufferedOutput()
        );

        Mockery::close();
    });

    it('returns early when sender and recipient cannot be determined', function () {
        Config::set('notifyre.default_sender');
        Config::set('notifyre.default_recipient');

        $mockService = Mockery::mock(NotifyreServiceInterface::class);
        $mockService->shouldNotReceive('send');

        $command = new NotifyreSmsSendCommand($mockService);

        $command->setLaravel(app());
        $command->setInput(new ArrayInput([
            'message' => 'Test message',
        ]));

        $command->run(
            new ArrayInput([
                'message' => 'Test message',
            ]),
            new BufferedOutput()
        );

        Mockery::close();
    });

    it('handles service exceptions gracefully', function () {
        $mockService = Mockery::mock(NotifyreServiceInterface::class);
        $mockService->shouldReceive('send')
            ->once()
            ->andThrow(new Exception('Service error'));

        $command = new NotifyreSmsSendCommand($mockService);

        $command->setLaravel(app());
        $command->setInput(new ArrayInput([
            'sender' => 'TestApp',
            'recipient' => '+0987654321',
            'message' => 'Test message',
        ]));

        $output = new BufferedOutput();

        $command->run(
            new ArrayInput([
                'sender' => 'TestApp',
                'recipient' => '+0987654321',
                'message' => 'Test message',
            ]),
            $output
        );

        expect($output->fetch())->toContain('Failed to send SMS: Service error');

        Mockery::close();
    });

    it('has correct signature', function () {
        $command = new NotifyreSmsSendCommand(Mockery::mock(NotifyreServiceInterface::class));

        expect($command->signature)->toBe('sms:send {sender? : The number the SMS will be sent from} {recipient? : The number the SMS will be sent to} {message? : The message that will be sent}');
    });

    it('has correct description', function () {
        $command = new NotifyreSmsSendCommand(Mockery::mock(NotifyreServiceInterface::class));

        expect($command->description)->toBe('Send an SMS to a specified phone number using Notifyre');
    });
});

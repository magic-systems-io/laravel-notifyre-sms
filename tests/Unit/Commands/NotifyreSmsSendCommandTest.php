<?php

namespace MagicSystemsIO\Notifyre\Tests\Unit\Commands;

use Illuminate\Console\Command;
use MagicSystemsIO\Notifyre\Commands\NotifyreSmsSendCommand;
use ReflectionClass;

test('can be instantiated', function () {
    $command = new NotifyreSmsSendCommand();

    expect($command)->toBeInstanceOf(NotifyreSmsSendCommand::class)
        ->and($command)->toBeInstanceOf(Command::class);
});

test('has correct signature structure', function () {
    $command = new NotifyreSmsSendCommand();

    $reflection = new ReflectionClass($command);
    $signatureProperty = $reflection->getProperty('signature');
    $signature = $signatureProperty->getValue($command);

    expect($signature)->toContain('sms:send')
        ->and($signature)->toContain('--s|sender=')
        ->and($signature)->toContain('--r|recipient=*')
        ->and($signature)->toContain('--m|message=');
});

test('has correct description', function () {
    $command = new NotifyreSmsSendCommand();

    $reflection = new ReflectionClass($command);
    $descriptionProperty = $reflection->getProperty('description');
    $description = $descriptionProperty->getValue($command);

    expect($description)->toBe('Send an SMS to a specified phone number using Notifyre');
});

test('retrieveArguments method exists and is private', function () {
    $command = new NotifyreSmsSendCommand();

    $reflection = new ReflectionClass($command);

    $method = $reflection->getMethod('parseMessage');
    expect($method->isPrivate())->toBeTrue();

    $method = $reflection->getMethod('parseRecipients');
    expect($method->isPrivate())->toBeTrue();

    $method = $reflection->getMethod('parseSender');
    expect($method->isPrivate())->toBeTrue();

});

test('handle method exists and is public', function () {
    $command = new NotifyreSmsSendCommand();

    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('handle');

    expect($method->isPublic())->toBeTrue();
});

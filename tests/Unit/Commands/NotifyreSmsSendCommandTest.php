<?php

namespace MagicSystemsIO\Notifyre\Tests\Unit\Commands;

use Illuminate\Console\Command;
use MagicSystemsIO\Notifyre\Commands\NotifyreSmsSendCommand;
use MagicSystemsIO\Notifyre\Services\NotifyreService;
use ReflectionClass;

test('can be instantiated', function () {
    $service = new NotifyreService();
    $command = new NotifyreSmsSendCommand($service);

    expect($command)->toBeInstanceOf(NotifyreSmsSendCommand::class)
        ->and($command)->toBeInstanceOf(Command::class);
});

test('has correct signature structure', function () {
    $service = new NotifyreService();
    $command = new NotifyreSmsSendCommand($service);

    $reflection = new ReflectionClass($command);
    $signatureProperty = $reflection->getProperty('signature');
    $signature = $signatureProperty->getValue($command);

    expect($signature)->toContain('sms:send')
        ->and($signature)->toContain('--sender=')
        ->and($signature)->toContain('--recipient=')
        ->and($signature)->toContain('--message=');
});

test('has correct description', function () {
    $service = new NotifyreService();
    $command = new NotifyreSmsSendCommand($service);

    $reflection = new ReflectionClass($command);
    $descriptionProperty = $reflection->getProperty('description');
    $description = $descriptionProperty->getValue($command);

    expect($description)->toBe('Send an SMS to a specified phone number using Notifyre');
});

test('retrieveArguments method exists and is private', function () {
    $service = new NotifyreService();
    $command = new NotifyreSmsSendCommand($service);

    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('retrieveArguments');

    expect($method->isPrivate())->toBeTrue();
});

test('handle method exists and is public', function () {
    $service = new NotifyreService();
    $command = new NotifyreSmsSendCommand($service);

    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('handle');

    expect($method->isPublic())->toBeTrue();
});

test('constructor accepts NotifyreService', function () {
    $service = new NotifyreService();
    $command = new NotifyreSmsSendCommand($service);

    // Use reflection to access protected properties
    $reflection = new ReflectionClass($command);
    $serviceProperty = $reflection->getProperty('service');
    $injectedService = $serviceProperty->getValue($command);

    expect($injectedService)->toBe($service);
});

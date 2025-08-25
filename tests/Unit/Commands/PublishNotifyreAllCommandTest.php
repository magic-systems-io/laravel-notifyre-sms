<?php

namespace MagicSystemsIO\Notifyre\Tests\Unit\Commands;

use Illuminate\Console\Command;
use MagicSystemsIO\Notifyre\Commands\PublishNotifyreAllCommand;
use ReflectionClass;

test('can be instantiated', function () {
    $command = new PublishNotifyreAllCommand();

    expect($command)->toBeInstanceOf(PublishNotifyreAllCommand::class)
        ->and($command)->toBeInstanceOf(Command::class);
});

test('has correct signature structure', function () {
    $command = new PublishNotifyreAllCommand();

    $reflection = new ReflectionClass($command);
    $signatureProperty = $reflection->getProperty('signature');
    $signature = $signatureProperty->getValue($command);

    expect($signature)->toContain('notifyre:publish')
        ->and($signature)->toContain('--force');
});

test('has correct description', function () {
    $command = new PublishNotifyreAllCommand();

    $reflection = new ReflectionClass($command);
    $descriptionProperty = $reflection->getProperty('description');
    $description = $descriptionProperty->getValue($command);

    expect($description)->toBe('Publish all Notifyre files (config and environment variables)');
});

test('handle method exists and is public', function () {
    $command = new PublishNotifyreAllCommand();

    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('handle');

    expect($method->isPublic())->toBeTrue();
});

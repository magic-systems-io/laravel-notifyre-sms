<?php

namespace MagicSystemsIO\Notifyre\Tests\Unit\Commands;

use Illuminate\Console\Command;
use MagicSystemsIO\Notifyre\Commands\PublishNotifyreConfigCommand;
use ReflectionClass;

beforeEach(function () {
    $this->command = new PublishNotifyreConfigCommand();
    $this->command->setLaravel(app());
});

test('can be instantiated', function () {
    expect($this->command)->toBeInstanceOf(PublishNotifyreConfigCommand::class)
        ->and($this->command)->toBeInstanceOf(Command::class);
});

test('has correct signature structure', function () {
    $reflection = new ReflectionClass($this->command);
    $signatureProperty = $reflection->getProperty('signature');
    $signature = $signatureProperty->getValue($this->command);

    expect($signature)->toContain('notifyre:publish-config')
        ->and($signature)->toContain('--force');
});

test('has correct description', function () {
    $reflection = new ReflectionClass($this->command);
    $descriptionProperty = $reflection->getProperty('description');
    $description = $descriptionProperty->getValue($this->command);

    expect($description)->toBe('Publish Notifyre configuration file');
});

test('shouldPublishConfig method exists and is private', function () {
    $reflection = new ReflectionClass($this->command);
    $method = $reflection->getMethod('shouldPublishConfig');

    expect($method->isPrivate())->toBeTrue();
});

test('handle method exists and is public', function () {
    $reflection = new ReflectionClass($this->command);
    $method = $reflection->getMethod('handle');

    expect($method->isPublic())->toBeTrue();
});

test('command can be instantiated with Laravel app', function () {
    expect($this->command)->toBeInstanceOf(PublishNotifyreConfigCommand::class);
});

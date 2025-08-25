<?php

namespace MagicSystemsIO\Notifyre\Tests\Unit\Commands;

use Illuminate\Console\Command;
use MagicSystemsIO\Notifyre\Commands\PublishNotifyreEnvCommand;
use ReflectionClass;

beforeEach(function () {
    $this->command = new PublishNotifyreEnvCommand();
    $this->command->setLaravel(app());
});

test('can be instantiated', function () {
    expect($this->command)->toBeInstanceOf(PublishNotifyreEnvCommand::class)
        ->and($this->command)->toBeInstanceOf(Command::class);
});

test('has correct signature structure', function () {
    $reflection = new ReflectionClass($this->command);
    $signatureProperty = $reflection->getProperty('signature');
    $signature = $signatureProperty->getValue($this->command);

    expect($signature)->toContain('notifyre:publish-env')
        ->and($signature)->toContain('--force');
});

test('has correct description', function () {
    $reflection = new ReflectionClass($this->command);
    $descriptionProperty = $reflection->getProperty('description');
    $description = $descriptionProperty->getValue($this->command);

    expect($description)->toBe('Publish Notifyre environment variables to .env file');
});

test('has correct environment variables array', function () {
    $reflection = new ReflectionClass($this->command);
    $property = $reflection->getProperty('envVariables');
    $envVariables = $property->getValue($this->command);

    expect($envVariables)->toHaveKey('NOTIFYRE_DRIVER')
        ->and($envVariables)->toHaveKey('NOTIFYRE_API_KEY')
        ->and($envVariables)->toHaveKey('NOTIFYRE_SMS_SENDER')
        ->and($envVariables)->toHaveKey('NOTIFYRE_SMS_RECIPIENT')
        ->and($envVariables)->toHaveKey('NOTIFYRE_BASE_URL')
        ->and($envVariables)->toHaveKey('NOTIFYRE_TIMEOUT')
        ->and($envVariables)->toHaveKey('NOTIFYRE_RETRY_TIMES')
        ->and($envVariables)->toHaveKey('NOTIFYRE_DB_ENABLED')
        ->and($envVariables)->toHaveKey('NOTIFYRE_CACHE_ENABLED')
        ->and($envVariables['NOTIFYRE_DRIVER'])->toBe('log')
        ->and($envVariables['NOTIFYRE_API_KEY'])->toBe('your_api_token_here')
        ->and($envVariables['NOTIFYRE_BASE_URL'])->toBe('https://api.notifyre.com')
        ->and($envVariables['NOTIFYRE_TIMEOUT'])->toBe(30);
});

test('handle method exists and is public', function () {
    $reflection = new ReflectionClass($this->command);
    $method = $reflection->getMethod('handle');

    expect($method->isPublic())->toBeTrue();
});

test('updateExistingEnvFile method exists and is private', function () {
    $reflection = new ReflectionClass($this->command);
    $method = $reflection->getMethod('updateExistingEnvFile');

    expect($method->isPrivate())->toBeTrue();
});

test('command can be instantiated with Laravel app', function () {
    expect($this->command)->toBeInstanceOf(PublishNotifyreEnvCommand::class);
});

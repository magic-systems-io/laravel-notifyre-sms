<?php

namespace Arbi\Notifyre\Tests\Feature\Commands;

use Arbi\Notifyre\Commands\PublishNotifyreEnvCommand;
use ReflectionClass;

describe('PublishNotifyreEnvCommand', function () {
    it('has correct signature', function () {
        $command = new PublishNotifyreEnvCommand();

        $reflection = new ReflectionClass($command);
        $property = $reflection->getProperty('signature');
        $property->setAccessible(true);
        $signature = $property->getValue($command);

        expect($signature)->toBe('notifyre:publish-env {--force : Force the operation to run without confirmation}');
    });

    it('has correct description', function () {
        $command = new PublishNotifyreEnvCommand();

        $reflection = new ReflectionClass($command);
        $property = $reflection->getProperty('description');
        $description = $property->getValue($command);

        expect($description)->toBe('Publish Notifyre environment variables to .env file');
    });

    it('has all required environment variables defined', function () {
        $command = new PublishNotifyreEnvCommand();

        $reflection = new ReflectionClass($command);
        $property = $reflection->getProperty('envVariables');
        $envVariables = $property->getValue($command);

        expect($envVariables)->toHaveKey('NOTIFYRE_API_TOKEN')
            ->and($envVariables)->toHaveKey('NOTIFYRE_DRIVER')
            ->and($envVariables)->toHaveKey('NOTIFYRE_TIMEOUT')
            ->and($envVariables)->toHaveKey('NOTIFYRE_RETRY_TIMES')
            ->and($envVariables)->toHaveKey('NOTIFYRE_RETRY_SLEEP')
            ->and($envVariables)->toHaveKey('NOTIFYRE_BASE_URL')
            ->and($envVariables)->toHaveKey('NOTIFYRE_SMS_SENDER')
            ->and($envVariables)->toHaveKey('NOTIFYRE_SMS_RECIPIENT')
            ->and($envVariables)->toHaveKey('NOTIFYRE_DEFAULT_NUMBER_PREFIX')
            ->and($envVariables)->toHaveKey('NOTIFYRE_SMS_DELAY')
            ->and($envVariables)->toHaveKey('NOTIFYRE_MAX_PER_MINUTE')
            ->and($envVariables)->toHaveKey('NOTIFYRE_CACHE_ENABLED')
            ->and($envVariables)->toHaveKey('NOTIFYRE_CACHE_TTL')
            ->and($envVariables)->toHaveKey('NOTIFYRE_CACHE_PREFIX')
            ->and(count($envVariables))->toBe(14);
    });

    it('has correct default values for key variables', function () {
        $command = new PublishNotifyreEnvCommand();

        $reflection = new ReflectionClass($command);
        $property = $reflection->getProperty('envVariables');
        $envVariables = $property->getValue($command);

        expect($envVariables['NOTIFYRE_DRIVER'])->toBe('log')
            ->and($envVariables['NOTIFYRE_TIMEOUT'])->toBe('30')
            ->and($envVariables['NOTIFYRE_RETRY_TIMES'])->toBe('3')
            ->and($envVariables['NOTIFYRE_RETRY_SLEEP'])->toBe('1000')
            ->and($envVariables['NOTIFYRE_BASE_URL'])->toBe('https://api.notifyre.com')
            ->and($envVariables['NOTIFYRE_CACHE_ENABLED'])->toBe('true')
            ->and($envVariables['NOTIFYRE_CACHE_TTL'])->toBe('3600')
            ->and($envVariables['NOTIFYRE_CACHE_PREFIX'])->toBe('notifyre_');
    });
});

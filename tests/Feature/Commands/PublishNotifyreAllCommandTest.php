<?php

namespace Arbi\Notifyre\Tests\Feature\Commands;

use Arbi\Notifyre\Commands\PublishNotifyreAllCommand;
use ReflectionClass;

describe('PublishNotifyreAllCommand', function () {
    it('has correct signature', function () {
        $command = new PublishNotifyreAllCommand();

        $reflection = new ReflectionClass($command);
        $property = $reflection->getProperty('signature');
        $signature = $property->getValue($command);

        expect($signature)->toBe('notifyre:publish {--force : Force the operation to run without confirmation}');
    });

    it('has correct description', function () {
        $command = new PublishNotifyreAllCommand();

        $reflection = new ReflectionClass($command);
        $property = $reflection->getProperty('description');
        $description = $property->getValue($command);

        expect($description)->toBe('Publish all Notifyre files (config and environment variables)');
    });
});

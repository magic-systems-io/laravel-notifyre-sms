<?php

namespace Arbi\Notifyre\Tests\Feature\Commands;

use Arbi\Notifyre\Commands\PublishNotifyreConfigCommand;
use ReflectionClass;

describe('PublishNotifyreConfigCommand', function () {
    it('has correct signature', function () {
        $command = new PublishNotifyreConfigCommand();

        $reflection = new ReflectionClass($command);
        $property = $reflection->getProperty('signature');
        $signature = $property->getValue($command);

        expect($signature)->toBe('notifyre:publish-config {--force : Force the operation to run without confirmation}');
    });

    it('has correct description', function () {
        $command = new PublishNotifyreConfigCommand();

        $reflection = new ReflectionClass($command);
        $property = $reflection->getProperty('description');
        $description = $property->getValue($command);

        expect($description)->toBe('Publish Notifyre configuration file');
    });
});

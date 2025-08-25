<?php

namespace MagicSystemsIO\Notifyre\Tests\Helpers;

/**
 * Helper functions for testing commands
 */

/**
 * Invoke a private method on an object
 */
function invokePrivateMethod($object, $methodName, ...$args)
{
    $reflection = new \ReflectionClass($object);
    $method = $reflection->getMethod($methodName);
    $method->setAccessible(true);

    return $method->invoke($object, ...$args);
}

/**
 * Get environment variables from PublishNotifyreEnvCommand for testing
 */
function getEnvVariables()
{
    $reflection = new \ReflectionClass(\MagicSystemsIO\Notifyre\Commands\PublishNotifyreEnvCommand::class);
    $property = $reflection->getProperty('envVariables');
    $property->setAccessible(true);
    $command = new \MagicSystemsIO\Notifyre\Commands\PublishNotifyreEnvCommand();

    return $property->getValue($command);
}

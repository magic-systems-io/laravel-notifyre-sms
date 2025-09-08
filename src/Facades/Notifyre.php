<?php

namespace MagicSystemsIO\Notifyre\Facades;

use BadMethodCallException;
use Illuminate\Support\Facades\Facade;
use InvalidArgumentException;
use MagicSystemsIO\Notifyre\Contracts\NotifyreManager;

/**
 * Facade for the Notifyre service.
 *
 * Provides methods for sending SMS through the Notifyre API
 *
 * @throws InvalidArgumentException
 * @throws BadMethodCallException
 *
 * @see \MagicSystemsIO\Notifyre\Services\NotifyreService
 */
class Notifyre extends Facade
{
    public static function __callStatic($method, $args)
    {
        $instance = static::resolveFacadeInstance(static::getFacadeAccessor());

        if (!method_exists($instance, $method)) {
            throw new BadMethodCallException("Method $method does not exist.");
        }

        return $instance->$method(...$args);
    }

    protected static function getFacadeAccessor(): string
    {
        return NotifyreManager::class;
    }
}

<?php

namespace Arbi\Notifyre\Enums;

enum NotifyreDriver: string
{
    case SMS = 'sms';
    case LOG = 'log';

    /**
     * Get all available driver values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Check if a driver value is valid
     */
    public static function isValid(string $driver): bool
    {
        return in_array($driver, self::values());
    }
}

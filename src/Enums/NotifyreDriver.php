<?php

namespace MagicSystemsIO\Notifyre\Enums;

enum NotifyreDriver: string
{
    case SMS = 'sms';
    case LOG = 'log';

    /**
     * Check if a driver value is valid
     */
    public static function isValid(string $driver): bool
    {
        return in_array($driver, self::values(), true);
    }

    /**
     * Get all available driver values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

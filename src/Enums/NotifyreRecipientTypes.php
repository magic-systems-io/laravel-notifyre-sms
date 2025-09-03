<?php

namespace MagicSystemsIO\Notifyre\Enums;

enum NotifyreRecipientTypes: string
{
    case MOBILE_NUMBER = 'mobile_number';
    case CONTACT = 'contact';
    case GROUP = 'group';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function isValid(string $type): bool
    {
        return in_array($type, self::values(), true);
    }
}

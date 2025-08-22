<?php

namespace Arbi\Notifyre\Enums;

enum NotifyreRecipientTypes: string
{
    case VIRTUAL_MOBILE_NUMBER = 'virtual_mobile_number';
    case CONTACT = 'contact';
    case GROUP = 'group';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function isValid(string $type): bool
    {
        return in_array($type, self::values());
    }
}

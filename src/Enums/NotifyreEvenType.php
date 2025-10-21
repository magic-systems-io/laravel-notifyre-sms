<?php

namespace MagicSystemsIO\Notifyre\Enums;

enum NotifyreEvenType: string
{
    case SMS_SENT = 'sms_sent';
    case SMS_RECEIVED = 'sms_received';
    case FAX_SENT = 'fax_sent';
    case FAX_RECEIVED = 'fax_received';
    case MMS_RECEIVED = 'mms_received';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

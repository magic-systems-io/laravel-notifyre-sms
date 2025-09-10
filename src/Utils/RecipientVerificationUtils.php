<?php

namespace MagicSystemsIO\Notifyre\Utils;

use InvalidArgumentException;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use MagicSystemsIO\Notifyre\Enums\NotifyreRecipientTypes;

class RecipientVerificationUtils
{
    /**
     * Check if phone number is valid
     *
     * @throws InvalidArgumentException
     */
    public static function validateRecipient(string $value, ?string $type  = NotifyreRecipientTypes::MOBILE_NUMBER->value): bool
    {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('Value cannot be empty');
        }

        if (!NotifyreRecipientTypes::isValid($type)) {
            throw new InvalidArgumentException("Invalid type '$type'. Valid types are: " . implode(', ', NotifyreRecipientTypes::values()));
        }

        return match(NotifyreRecipientTypes::from($type)) {
            NotifyreRecipientTypes::MOBILE_NUMBER => self::validatePhoneNumber($value),
            NotifyreRecipientTypes::CONTACT => self::validateContact($value),
            NotifyreRecipientTypes::GROUP => self::validateGroup($value),
        };
    }

    private static function validatePhoneNumber(string $value): bool
    {
        try {
            $phoneUtil = PhoneNumberUtil::getInstance();
            $phoneNumber = $phoneUtil->parse($value);

            return $phoneUtil->isValidNumber($phoneNumber);
        } catch (NumberParseException) {
            return false;
        }
    }

    private static function validateContact(string $value): bool
    {
        return false;
    }

    private static function validateGroup(string $value): bool
    {
        return false;
    }
}

<?php

namespace Arbi\Notifyre\DTO\SMS;

use Symfony\Component\Mime\Exception\InvalidArgumentException;

readonly class Recipient
{
    public const array VALID_TYPES = [
        'mobile_number',
        'contact',
        'group',
    ];

    public function __construct(
        public string $type,
        public string $value,
    ) {
        if (!in_array($type, self::VALID_TYPES)) {
            throw new InvalidArgumentException("Invalid type '$type'. Valid types are: " . implode(', ', self::VALID_TYPES));
        }

        if (empty(trim($value))) {
            throw new InvalidArgumentException('Value cannot be empty');
        }
    }
}

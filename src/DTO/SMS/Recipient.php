<?php

namespace MagicSystemsIO\Notifyre\DTO\SMS;

use Illuminate\Contracts\Support\Arrayable;
use MagicSystemsIO\Notifyre\Enums\NotifyreRecipientTypes;
use Symfony\Component\Mime\Exception\InvalidArgumentException;

readonly class Recipient implements Arrayable
{
    public function __construct(
        public string $type,
        public string $value,
    ) {
        if (!NotifyreRecipientTypes::isValid($this->type)) {
            throw new InvalidArgumentException("Invalid type '$type'. Valid types are: " . implode(', ', NotifyreRecipientTypes::values()));
        }

        if (empty(trim($value))) {
            throw new InvalidArgumentException('Value cannot be empty');
        }
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'value' => $this->value,
        ];
    }
}

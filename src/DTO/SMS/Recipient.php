<?php

namespace MagicSystemsIO\Notifyre\DTO\SMS;

use Illuminate\Contracts\Support\Arrayable;
use MagicSystemsIO\Notifyre\Utils\RecipientVerificationUtils;
use Symfony\Component\Mime\Exception\InvalidArgumentException;

readonly class Recipient implements Arrayable
{
    public function __construct(
        public string $type,
        public string $value,
    ) {
        if (!RecipientVerificationUtils::validateRecipient($this->normalizeCountryCode($this->value), $this->type)) {
            throw new InvalidArgumentException("Invalid recipient '$value' for type '$type'.");
        }
    }

    private function normalizeCountryCode(string $value): string
    {
        if (str_starts_with($value, '+')) {
            return $value;
        }

        return preg_replace('/^0/', config('notifyre.default_number_prefix'), $value);
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'value' => $this->value,
        ];
    }
}

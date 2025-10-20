<?php

namespace MagicSystemsIO\Notifyre\DTO\SMS;

use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;
use MagicSystemsIO\Notifyre\Utils\RecipientVerificationUtils;

class Recipient implements Arrayable
{
    public function __construct(
        public string $type,
        public string $value,
    ) {
        $normalizedValue = $this->normalizeValue();

        if (!RecipientVerificationUtils::validateRecipient($normalizedValue, $type)) {
            throw new InvalidArgumentException("Invalid recipient '$normalizedValue' for type '$type'.");
        }

        $this->value = $normalizedValue;
    }

    private function normalizeValue(): string
    {
        if ($this->type !== 'mobile_number') {
            return $this->value;
        }

        // Remove all characters except digits and plus sign
        $value = preg_replace('/[^\d+]/', '', $this->value);

        if (!str_starts_with($value, '+')) {
            $defaultPrefix = config('notifyre.default_number_prefix');

            if (empty($defaultPrefix)) {
                throw new InvalidArgumentException('Recipient number must include country code or set a default prefix in configuration.');
            }

            $value = preg_replace('/^0/', $defaultPrefix, $value);
        }

        return $value;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'value' => $this->value,
        ];
    }
}

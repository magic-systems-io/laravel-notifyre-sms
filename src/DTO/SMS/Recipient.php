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
        $this->normalizeCountryCode();
        if (!RecipientVerificationUtils::validateRecipient($this->value, $this->type)) {
            throw new InvalidArgumentException("Invalid recipient '$value' for type '$type'.");
        }
    }

    private function normalizeCountryCode(): void
    {
        if ($this->type !== 'mobile_number') {
            return;
        }

        if (!str_starts_with($this->value, '+')) {
            $defaultPrefix = config('notifyre.default_number_prefix');
            if (empty($defaultPrefix)) {
                throw new InvalidArgumentException('Recipient number must include country code or set a default prefix in configuration.');
            }
            $this->value = preg_replace('/^0/', $defaultPrefix, $this->value);
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

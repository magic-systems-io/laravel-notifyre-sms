<?php

namespace MagicSystemsIO\Notifyre\Enums;

enum NotifyProcessedStatus: string
{
    case SENT = 'sent';
    case DELIVERED = 'delivered';
    case QUEUED = 'queued';
    case FAILED = 'failed';
    case PENDING = 'pending';
    case UNDELIVERED = 'undelivered';
    case UNDELIVERABLE = 'undeliverable';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function isValid(string $value): bool
    {
        return self::tryFrom($value) !== null;
    }

    public function isSuccessful(): bool
    {
        return in_array($this, [self::SENT, self::DELIVERED]);
    }

    public static function fromNullableString(?string $status): ?self
    {
        if ($status === null) {
            return null;
        }

        return self::tryFrom(strtolower($status));
    }

    public static function isStatusSuccessful(?string $status): bool
    {
        $enumStatus = self::fromNullableString($status);

        return $enumStatus?->isSuccessful() ?? false;
    }
}

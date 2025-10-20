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

    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
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

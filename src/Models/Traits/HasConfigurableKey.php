<?php

namespace MagicSystemsIO\Notifyre\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Trait that provides configurable UUID or auto-increment key support for models.
 * Uses the config 'notifyre.database.use_uuid' to determine the key type.
 *
 * When UUID mode is enabled:
 * - Automatically generates UUIDs for new models
 * - Sets key type to 'string'
 * - Disables auto-incrementing
 *
 * When UUID mode is disabled:
 * - Uses standard auto-incrementing integer IDs
 * - Sets key type to 'int'
 * - Enables auto-incrementing
 */
trait HasConfigurableKey
{
    private static bool $use_uuid;

    /**
     * Boot the trait and attach the UUID generation logic if configured.
     */
    public static function bootHasConfigurableKey(): void
    {
        self::$use_uuid = config('notifyre.database.use_uuid', true);

        if (self::$use_uuid) {
            static::creating(function (Model $model) {
                $keyName = $model->getKeyName();
                if (empty($model->getAttribute($keyName))) {
                    $model->setAttribute($keyName, Str::uuid()->toString());
                }
            });
        }
    }

    /**
     * Get the key type for the model.
     */
    public function getKeyType(): string
    {
        return self::$use_uuid ? 'string' : 'int';
    }

    /**
     * Determine if the IDs are incrementing.
     */
    public function getIncrementing(): bool
    {
        return !self::$use_uuid;
    }
}

<?php

namespace MagicSystemsIO\Notifyre\Services;

use Illuminate\Support\Facades\Log;

class NotifyreLogger
{
    private static string $prefix;

    private static ?string $customChannel = null;

    private static bool $initialized = false;

    /**
     * Log an info message with notifyre prefix
     */
    public static function info(string $message, array $context = []): void
    {
        self::log('info', $message, $context);
    }

    /**
     * Log a message with the specified level and notifyre prefix
     */
    public static function log(string $level, string $message, array $context = []): void
    {
        self::initialize();

        $prefixedMessage = self::formatMessage($message);

        if (self::$customChannel) {
            Log::channel(self::$customChannel)->log($level, $prefixedMessage, $context);
        } else {
            Log::log($level, $prefixedMessage, $context);
        }
    }

    /**
     * Initialize the logger configuration
     */
    private static function initialize(): void
    {
        if (self::$initialized) {
            return;
        }

        self::$prefix = config('notifyre.logging.prefix', 'notifyre_sms');
        self::setupCustomChannel();
        self::$initialized = true;
    }

    /**
     * Set up custom channel for file-based logging
     */
    private static function setupCustomChannel(): void
    {
        $defaultChannel = config('logging.default', 'stack');
        $defaultConfig = config("logging.channels.$defaultChannel", []);

        self::$customChannel = 'notifyre_sms';

        if (!config('logging.channels.' . self::$customChannel)) {
            $driver = $defaultConfig['driver'] ?? 'single';

            if ($driver === 'stack') {
                $driver = 'single';
            }

            $customConfig = [
                'driver' => $driver,
                'path' => storage_path('logs/' . self::$prefix . '.log'),
                'level' => config('logging.level', 'debug'),
            ];

            if ($driver === 'daily') {
                $customConfig['days'] = $defaultConfig['days'] ?? 14;
            }

            config(['logging.channels.' . self::$customChannel => $customConfig]);
        }
    }

    /**
     * Format the message with the notifyre prefix
     */
    private static function formatMessage(string $message): string
    {
        return '[' . self::$prefix . "] $message";
    }

    /**
     * Log a warning message with notifyre prefix
     */
    public static function warning(string $message, array $context = []): void
    {
        self::log('warning', $message, $context);
    }

    /**
     * Log an error message with notifyre prefix
     */
    public static function error(string $message, array $context = []): void
    {
        self::log('error', $message, $context);
    }

    /**
     * Log a debug message with notifyre prefix
     */
    public static function debug(string $message, array $context = []): void
    {
        self::log('debug', $message, $context);
    }

    /**
     * Get the prefix
     */
    public static function getPrefix(): string
    {
        self::initialize();

        return self::$prefix;
    }
}

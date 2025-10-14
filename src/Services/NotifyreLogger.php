<?php

namespace MagicSystemsIO\Notifyre\Services;

use Illuminate\Support\Facades\Log;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class NotifyreLogger
{
    private static string $prefix;

    private static ?string $customChannel = null;

    private static bool $initialized = false;

    public static function info(string $message, array $context = []): void
    {
        self::log('info', $message, $context);
    }

    public static function log(string $level, string $message, array $context = []): void
    {
        if (!config('notifyre.logging.enabled', true)) {
            return;
        }

        self::initialize();

        $prefixedMessage = self::formatMessage($message);

        if (self::$customChannel) {
            Log::channel(self::$customChannel)->log($level, $prefixedMessage, $context);
        } else {
            Log::log($level, $prefixedMessage, $context);
        }
    }

    private static function initialize(): void
    {
        if (self::$initialized) {
            return;
        }

        self::$prefix = config('notifyre.logging.prefix', 'notifyre_sms');
        self::setupCustomChannel();
        self::$initialized = true;
    }

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
                'level' => config('notifyre.logging.level', config('logging.level', 'debug')),
            ];

            if ($driver === 'daily') {
                $customConfig['days'] = $defaultConfig['days'] ?? 14;
            }

            config(['logging.channels.' . self::$customChannel => $customConfig]);
        }
    }

    private static function formatMessage(string $message): string
    {
        return '[' . self::$prefix . "] $message";
    }

    public static function warning(string $message, array $context = []): void
    {
        self::log('warning', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::log('error', $message, $context);
    }

    public static function debug(string $message, array $context = []): void
    {
        self::log('debug', $message, $context);
    }

    public static function getPrefix(): string
    {
        self::initialize();

        return self::$prefix;
    }

    public function __invoke(array $config): Logger
    {
        $logger = new Logger('notifyre');

        if (!config('notifyre.logging.enabled', true)) {
            return $logger;
        }

        $prefix = config('notifyre.logging.prefix', 'notifyre_sms');
        $logPath = storage_path('logs/' . $prefix . '.log');

        $logLevel = $this->getLogLevel();

        $defaultChannel = config('logging.default', 'stack');
        $defaultConfig = config("logging.channels.$defaultChannel", []);
        $driver = $defaultConfig['driver'] ?? 'single';

        if ($driver === 'stack') {
            $driver = 'single';
        }

        if ($driver === 'daily') {
            $days = $defaultConfig['days'] ?? 14;
            $handler = new RotatingFileHandler($logPath, $days, $logLevel);
        } else {
            $handler = new StreamHandler($logPath, $logLevel);
        }

        $handler->setFormatter(new LineFormatter(
            "[%datetime%] %channel%.%level_name%: [$prefix] %message% %context% %extra%\n"
        ));

        $logger->pushHandler($handler);

        return $logger;
    }

    private function getLogLevel(): int
    {
        $level = config('notifyre.logging.level', config('logging.level', 'debug'));

        if (!config('app.debug') && $level === 'debug') {
            $level = 'info';
        }

        return match (strtolower($level)) {
            'emergency' => Logger::EMERGENCY,
            'alert' => Logger::ALERT,
            'critical' => Logger::CRITICAL,
            'error' => Logger::ERROR,
            'warning' => Logger::WARNING,
            'notice' => Logger::NOTICE,
            'info' => Logger::INFO,
            'debug' => Logger::DEBUG,
            default => Logger::DEBUG,
        };
    }
}

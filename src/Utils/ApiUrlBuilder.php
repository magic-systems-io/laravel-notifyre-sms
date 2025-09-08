<?php

namespace MagicSystemsIO\Notifyre\Utils;

use InvalidArgumentException;

class ApiUrlBuilder
{
    /**
     * Construct API URL for SMS operations
     *
     * @throws InvalidArgumentException
     */
    public static function buildSmsUrl(?string $messageId = null): string
    {
        $baseUrl = self::getBaseUrl();
        $path = '/sms/send';

        if ($messageId) {
            $path .= '/' . $messageId;
        }

        return rtrim($baseUrl, '/') . $path;
    }

    /**
     * Get base URL from configuration
     *
     * @throws InvalidArgumentException
     */
    private static function getBaseUrl(): string
    {
        $url = config('notifyre.base_url') ?? '';
        if (empty(trim($url))) {
            throw new InvalidArgumentException('Notifyre base URL is not configured.');
        }

        return $url;
    }

    /**
     * Build URL with query parameters
     */
    public static function buildUrlWithQuery(string $baseUrl, array $queryParams = []): string
    {
        if (empty($queryParams)) {
            return $baseUrl;
        }

        return $baseUrl . '?' . http_build_query($queryParams);
    }
}

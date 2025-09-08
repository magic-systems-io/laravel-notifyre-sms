<?php

namespace MagicSystemsIO\Notifyre\Utils;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;

class ApiClientUtils
{
    /**
     * Make HTTP request to API
     *
     * @throws ConnectionException
     * @throws InvalidArgumentException
     */
    public static function request(string $url, RequestBody $body = null, string $method = 'GET'): PromiseInterface|Response
    {
        $httpClient = self::createHttpClient();

        return match($method) {
            'POST' => $httpClient->post($url, $body?->toArray() ?? []),
            'GET' => $httpClient->get($url),
            default => throw new InvalidArgumentException("Unsupported HTTP method: $method")
        };
    }

    /**
     * Create configured HTTP client with headers, timeout, and retry logic
     */
    public static function createHttpClient(): PendingRequest
    {
        return Http::timeout(config('notifyre.timeout', 30))
            ->retry(
                times: config('notifyre.retry.times', 3),
                sleepMilliseconds: config('notifyre.retry.sleep', 1000),
            )
            ->withHeaders([
                'x-api-token' => self::getApiKey(),
                'Content-Type' => 'application/json',
            ]);
    }

    /**
     * Get API key from configuration
     *
     * @throws InvalidArgumentException
     */
    public static function getApiKey(): string
    {
        $apiKey = config('services.notifyre.api_key') ?? config('notifyre.api_key') ?? '';
        if (empty(trim($apiKey))) {
            throw new InvalidArgumentException('Notifyre API key is not configured.');
        }

        return $apiKey;
    }
}

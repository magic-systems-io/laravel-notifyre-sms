<?php

namespace Arbi\Notifyre\Services\Drivers;

use Arbi\Notifyre\Contracts\NotifyreDriverInterface;
use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Arbi\Notifyre\DTO\SMS\ResponseBodyDTO;
use Arbi\Notifyre\DTO\SMS\ResponsePayload;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

readonly class SMSDriver implements NotifyreDriverInterface
{
    /**
     * @throws InvalidArgumentException
     * @throws ConnectionException
     */
    public function send(RequestBodyDTO $requestBody): void
    {
        $url = $this->getApiUrl();
        $apiKey = $this->getApiKey();

        $data = [
            'Body' => $requestBody->body,
            'Recipients' => array_map(function (Recipient $recipient) {
                return [
                    'type' => $recipient->type,
                    'value' => $recipient->value,
                ];
            }, $requestBody->recipients),
        ];

        $response = Http::timeout(config('notifyre.timeout', 30))
            ->retry(
                config('notifyre.retry.times', 3),
                config('notifyre.retry.sleep', 1000)
            )
            ->withHeaders([
                'x-api-token' => $apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post($url, $data);

        if (!$response->successful()) {
            throw new ConnectionException("Failed to send SMS: {$response->body()}");
        }

        $this->cache($response->json(), $response->status());
    }

    /**
     * @throws InvalidArgumentException
     */
    private function getApiUrl(): string
    {
        $url = config('notifyre.base_url') ?? '';
        if (empty(trim($url))) {
            throw new InvalidArgumentException('Notifyre base URL is not configured.');
        }

        return rtrim($url, '/') . '/sms/send';
    }

    /**
     * @throws InvalidArgumentException
     */
    private function getApiKey(): string
    {
        $apiKey = config('services.notifyre.api_key') ?? config('notifyre.api_key') ?? '';
        if (empty(trim($apiKey))) {
            throw new InvalidArgumentException('Notifyre API key is not configured.');
        }

        return $apiKey;
    }

    private function cache(array $json, int $status): void
    {
        if (!config('notifyre.cache.enabled')) {
            return;
        }

        $parsed = $this->parseResponse($json, $status);
        // todo: Implement caching logic here
    }

    private function parseResponse(array $responseData, int $statusCode): ResponseBodyDTO
    {
        $payload = new ResponsePayload(
            smsMessageID: $responseData['payload']['smsMessageID'] ?? '',
            friendlyID: $responseData['payload']['friendlyID'] ?? '',
            invalidToNumbers: $responseData['payload']['invalidToNumbers'] ?? []
        );

        return new ResponseBodyDTO(
            success: $responseData['success'] ?? false,
            statusCode: $responseData['statusCode'] ?? $statusCode,
            message: $responseData['message'] ?? '',
            payload: $payload,
            errors: $responseData['errors'] ?? []
        );
    }
}

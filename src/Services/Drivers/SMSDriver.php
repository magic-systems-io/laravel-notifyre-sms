<?php

namespace MagicSystemsIO\Notifyre\Services\Drivers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use MagicSystemsIO\Notifyre\Contracts\NotifyreManager;
use MagicSystemsIO\Notifyre\DTO\SMS\InvalidNumber;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponseBody;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponsePayload;

readonly class SMSDriver implements NotifyreManager
{
    /**
     * @param RequestBody $request
     *
     *@throws ConnectionException
     * @return ?ResponseBody
     */
    public function send(RequestBody $request): ?ResponseBody
    {
        $url = $this->getApiUrl();
        $apiKey = $this->getApiKey();

        $response = Http::timeout(config('notifyre.timeout'))
            ->retry(
                config('notifyre.retry.times', 3),
                config('notifyre.retry.sleep', 1000)
            )
            ->withHeaders([
                'x-api-token' => $apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post($url, $request->toArray());

        return $this->parseResponse($response->json(), $response->status());
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

    private function parseResponse(array $responseData, int $statusCode): ResponseBody
    {
        if (isset($responseData['Payload']['InvalidToNumbers']) && is_array($responseData['Payload']['InvalidToNumbers'])) {
            $invalidToNumbers = array_map(fn ($invalidNumber) => new InvalidNumber(
                number: $invalidNumber['Number'] ?? '',
                message: $invalidNumber['Message'] ?? ''
            ), $responseData['Payload']['InvalidToNumbers']);
        }

        $payload = new ResponsePayload(
            smsMessageID: $responseData['Payload']['SmsMessageID'] ?? '',
            friendlyID: $responseData['Payload']['FriendlyID'] ?? '',
            invalidToNumbers: $invalidToNumbers ?? [],
        );

        return new ResponseBody(
            success: $responseData['Success'] ?? false,
            statusCode: $responseData['StatusCode'] ?? $statusCode,
            message: $responseData['Message'] ?? '',
            payload: $payload,
            errors: $responseData['Errors'] ?? []
        );
    }
}

<?php

namespace MagicSystemsIO\Notifyre\Services\Drivers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponseBody;
use MagicSystemsIO\Notifyre\Utils\ApiClientUtils;
use MagicSystemsIO\Notifyre\Utils\ApiUrlBuilder;
use MagicSystemsIO\Notifyre\Utils\ResponseParser;

readonly class SmsDriver
{
    /**
     * Send SMS message
     *
     * @throws ConnectionException
     */
    public function send(RequestBody $request): ResponseBody
    {
        $response = ApiClientUtils::request(ApiUrlBuilder::buildSmsUrl(), $request, 'POST');

        Log::channel('notifyre')->info('Notifyre SMS send', ['response' => $response]);

        return ResponseParser::parseSmsResponse($response->json(), $response->status());
    }

    /**
     * Get SMS message by ID
     *
     * @throws ConnectionException
     */
    public function get(string $messageId): ?ResponseBody
    {
        $response = ApiClientUtils::request(ApiUrlBuilder::buildSmsUrl($messageId));
        Log::channel('notifyre')->info('Notifyre SMS get', ['response' => $response]);

        return ResponseParser::parseSmsResponse($response->json(), $response->status());
    }

    /**
     * List SMS messages with optional query parameters
     *
     * @throws ConnectionException
     * @return ResponseBody[]
     *
     * @see https://docs.notifyre.com/api/sms-sent-list the available query parameters
     */
    public function list(array $queryParams): array
    {
        $response = ApiClientUtils::request(
            ApiUrlBuilder::buildUrlWithQuery(
                baseUrl:     ApiUrlBuilder::buildSmsUrl(),
                queryParams: $queryParams
            )
        );
        Log::channel('notifyre')->info('Notifyre SMS list', ['response' => $response]);

        $payload = $response->json('payload', []);
        $smsMessages = $payload['smsMessages'] ?? [];

        return ResponseParser::parseSmsListResponse($smsMessages, $response->status());
    }
}

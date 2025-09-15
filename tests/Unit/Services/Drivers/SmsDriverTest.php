<?php

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponseBody;
use MagicSystemsIO\Notifyre\Services\Drivers\SmsDriver;
use MagicSystemsIO\Notifyre\Utils\ApiUrlBuilder;

beforeEach(function () {
    config([
        'services.notifyre.driver' => 'sms',
        'services.notifyre.api_key' => 'test_api_key',
        'notifyre.base_url' => 'https://api.notifyre.com',
        'notifyre.http.timeout' => 5,
        'notifyre.http.retry.times' => 1,
        'notifyre.http.retry.sleep' => 1,
    ]);

    $this->driver = new SmsDriver();
});

it('can be instantiated', function () {
    expect($this->driver)->toBeInstanceOf(SmsDriver::class);
});

it('can send SMS and returns parsed response', function () {
    $request = createRequestBody();
    $expectedResponse = createResponsePayload(['id' => 'msg_123', 'status' => 'sent']);
    $smsUrl = ApiUrlBuilder::buildSmsUrl();

    Http::fake([
        $smsUrl => Http::response([
            'success' => true,
            'statusCode' => 200,
            'message' => 'Success',
            'payload' => $expectedResponse->toArray(),
            'errors' => [],
        ]),
    ]);

    $result = $this->driver->send($request);

    expect($result)->toBeInstanceOf(ResponseBody::class)
        ->and($result->payload->id)->toBe('msg_123')
        ->and($result->payload->status)->toBe('sent')
        ->and($result->success)->toBeTrue();

    Http::assertSent(function ($httpRequest) use ($smsUrl) {
        return $httpRequest->url() === $smsUrl
            && $httpRequest->method() === 'POST'
            && $httpRequest->hasHeader('x-api-token', 'test_api_key')
            && $httpRequest->hasHeader('Content-Type', 'application/json');
    });
});

it('can get a message by id and returns parsed response', function () {
    $messageId = 'message-id-123';
    $smsUrl = ApiUrlBuilder::buildSmsUrl($messageId);

    $expectedResponse = createResponsePayload(['id' => $messageId, 'status' => 'delivered']);

    Http::fake(callback: [
        $smsUrl => Http::response([
            'success' => true,
            'statusCode' => 200,
            'message' => 'Success',
            'payload' => $expectedResponse->toArray(),
            'errors' => [],
        ]),
    ]);

    $result = $this->driver->get($messageId);

    expect($result)->toBeInstanceOf(ResponseBody::class)
        ->and($result->payload->id)->toBe($messageId)
        ->and($result->payload->status)->toBe('delivered')
        ->and($result->success)->toBeTrue();

    Http::assertSent(function ($httpRequest) use ($smsUrl) {
        return $httpRequest->url() === $smsUrl
            && $httpRequest->method() === 'GET'
            && $httpRequest->hasHeader('x-api-token', 'test_api_key');
    });
});

it('can list messages and returns parsed list', function () {
    $queryParams = ['page' => 1];
    $smsUrl = ApiUrlBuilder::buildUrlWithQuery(
        baseUrl: ApiUrlBuilder::buildSmsUrl(),
        queryParams: $queryParams
    );

    $message1 = createResponsePayload(['id' => 'msg_1', 'status' => 'sent']);
    $message2 = createResponsePayload(['id' => 'msg_2', 'status' => 'sent']);

    Http::fake([
        $smsUrl => Http::response([
            'payload' => [
                'smsMessages' => [
                    $message1->toArray(),
                    $message2->toArray(),
                ],
            ],
        ]),
    ]);

    $result = $this->driver->list($queryParams);

    expect($result)->toBeArray()
        ->and($result)->toHaveCount(2)
        ->and($result[0])->toBeInstanceOf(ResponseBody::class)
        ->and($result[0]->payload->id)->toBe('msg_1')
        ->and($result[1]->payload->id)->toBe('msg_2');

    Http::assertSent(function ($httpRequest) use ($smsUrl) {
        return $httpRequest->url() === $smsUrl
            && $httpRequest->method() === 'GET'
            && $httpRequest->hasHeader('x-api-token', 'test_api_key');
    });
});

it('rethrows connection exceptions from API client', function () {
    $request = createRequestBody();
    $smsUrl = ApiUrlBuilder::buildSmsUrl();

    Http::fake([
        $smsUrl => function () {
            throw new ConnectionException('Connection failed');
        },
    ]);

    expect(fn () => $this->driver->send($request))
        ->toThrow(ConnectionException::class, 'Connection failed');
});

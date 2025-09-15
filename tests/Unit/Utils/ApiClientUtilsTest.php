<?php

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use MagicSystemsIO\Notifyre\Utils\ApiClientUtils;

it('returns api key from services.notifyre.api_key when configured', function () {
    config([
        'services.notifyre.api_key' => 'svc_key_123',
        'notifyre.http.timeout' => 5,
        'notifyre.http.retry.times' => 1,
        'notifyre.http.retry.sleep' => 1,
    ]);

    expect(ApiClientUtils::getApiKey())->toBe('svc_key_123');
});

it('falls back to notifyre.api_key when services.notifyre.api_key is not set', function () {
    config([
        'services.notifyre.api_key' => null,
        'notifyre.api_key' => 'root_key_456',
        'notifyre.http.timeout' => 5,
        'notifyre.http.retry.times' => 1,
        'notifyre.http.retry.sleep' => 1,
    ]);

    expect(ApiClientUtils::getApiKey())->toBe('root_key_456');
});

it('throws when api key is not configured or is blank', function () {
    config([
        'services.notifyre.api_key' => null,
        'notifyre.api_key' => '   ',
        'notifyre.http.timeout' => 5,
        'notifyre.http.retry.times' => 1,
        'notifyre.http.retry.sleep' => 1,
    ]);

    expect(fn () => ApiClientUtils::getApiKey())->toThrow(InvalidArgumentException::class);
});

it('sends a POST request with JSON body and x-api-token header', function () {
    config([
        'services.notifyre.api_key' => 'post_key_abc',
        'notifyre.http.timeout' => 5,
        'notifyre.http.retry.times' => 1,
        'notifyre.http.retry.sleep' => 1,
    ]);

    Http::fake([
        'https://api.test/send' => Http::response(['success' => true]),
    ]);

    $body = createRequestBody();

    $response = ApiClientUtils::request('https://api.test/send', $body, 'POST');

    expect($response)->toBeInstanceOf(Response::class);

    Http::assertSent(function ($request) use ($body) {
        $headers = $request->headers();
        $hasHeader = isset($headers['x-api-token'][0]) && $headers['x-api-token'][0] === config('services.notifyre.api_key');
        $bodyMatches = $request->body() === json_encode($body->toArray());

        return $request->method() === 'POST' && $hasHeader && $bodyMatches;
    });
});

it('sends a GET request and returns a response', function () {
    config([
        'services.notifyre.api_key' => 'get_key_def',
        'notifyre.http.timeout' => 5,
        'notifyre.http.retry.times' => 1,
        'notifyre.http.retry.sleep' => 1,
    ]);

    Http::fake([
        'https://api.test/ping' => Http::response(['ok' => true]),
    ]);

    $response = ApiClientUtils::request('https://api.test/ping');

    expect($response)->toBeInstanceOf(Response::class);

    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.test/ping' && $request->method() === 'GET';
    });
});

it('throws for unsupported HTTP methods', function () {
    config([
        'services.notifyre.api_key' => 'unused_key',
        'notifyre.http.timeout' => 5,
        'notifyre.http.retry.times' => 1,
        'notifyre.http.retry.sleep' => 1,
    ]);

    expect(fn () => ApiClientUtils::request('https://api.test/unsupported', null, 'PUT'))
        ->toThrow(InvalidArgumentException::class);
});

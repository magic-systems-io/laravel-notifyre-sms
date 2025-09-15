<?php

use MagicSystemsIO\Notifyre\DTO\SMS\ResponseBody;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponsePayload;

it('can instantiate ResponseBody DTO with default values', function () {
    $response = createResponseBody();
    expect($response)->toBeInstanceOf(ResponseBody::class)
        ->and($response->success)->toBeTrue()
        ->and($response->statusCode)->toBe(200)
        ->and($response->message)->toBe('Success')
        ->and($response->payload)->toBeInstanceOf(ResponsePayload::class)
        ->and($response->errors)->toBeArray()
        ->and($response->errors)->toBe([]);
});

it('can instantiate ResponseBody DTO with custom values', function () {
    $payload = createResponsePayload(['id' => 'custom_id']);
    $response = createResponseBody([
        'success' => false,
        'statusCode' => 404,
        'message' => 'Not found',
        'payload' => $payload,
        'errors' => ['error1', 'error2'],
    ]);
    expect($response->success)->toBeFalse()
        ->and($response->statusCode)->toBe(404)
        ->and($response->message)->toBe('Not found')
        ->and($response->payload->id)->toBe('custom_id')
        ->and($response->errors)->toBe(['error1', 'error2']);
});

it('can convert ResponseBody DTO to array', function () {
    $payload = createResponsePayload(['id' => 'payload_999']);
    $response = createResponseBody([
        'success' => true,
        'statusCode' => 201,
        'message' => 'Created',
        'payload' => $payload,
        'errors' => ['foo', 'bar'],
    ]);
    $array = $response->toArray();
    expect($array['success'])->toBeTrue()
        ->and($array['status_code'])->toBe(201)
        ->and($array['message'])->toBe('Created')
        ->and($array['payload'])->toBe($payload->toArray())
        ->and($array['errors'])->toBe(['foo', 'bar']);
});

it('handles empty errors and null payload gracefully', function () {
    $payload = createResponsePayload();
    $response = createResponseBody([
        'errors' => [],
        'payload' => $payload,
    ]);
    expect($response->errors)->toBe([])
        ->and($response->payload)->toBeInstanceOf(ResponsePayload::class);
});

it('can override all fields using helper', function () {
    $payload = createResponsePayload(['id' => 'override_id']);
    $response = createResponseBody([
        'success' => false,
        'statusCode' => 500,
        'message' => 'Server error',
        'payload' => $payload,
        'errors' => ['server_error'],
    ]);
    expect($response->success)->toBeFalse()
        ->and($response->statusCode)->toBe(500)
        ->and($response->message)->toBe('Server error')
        ->and($response->payload->id)->toBe('override_id')
        ->and($response->errors)->toBe(['server_error']);
});

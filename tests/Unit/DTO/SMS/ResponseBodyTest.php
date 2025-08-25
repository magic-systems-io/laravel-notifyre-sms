<?php

namespace MagicSystemsIO\Notifyre\Tests\Unit\DTO\SMS;

use MagicSystemsIO\Notifyre\DTO\SMS\ResponseBody;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponsePayload;

use function MagicSystemsIO\Notifyre\Tests\Helpers\build_error_response_body_bad_request;
use function MagicSystemsIO\Notifyre\Tests\Helpers\build_error_response_body_rate_limited;
use function MagicSystemsIO\Notifyre\Tests\Helpers\build_error_response_body_server_error;
use function MagicSystemsIO\Notifyre\Tests\Helpers\build_error_response_body_unauthorized;
use function MagicSystemsIO\Notifyre\Tests\Helpers\build_error_response_body_validation_errors;
use function MagicSystemsIO\Notifyre\Tests\Helpers\build_success_response_body;
use function MagicSystemsIO\Notifyre\Tests\Helpers\build_success_response_body_with_invalid_numbers;

test('can be instantiated with success response', function () {
    $response_body = build_success_response_body();

    expect($response_body)->toBeInstanceOf(ResponseBody::class)
        ->and($response_body->success)->toBeTrue()
        ->and($response_body->statusCode)->toBe(200)
        ->and($response_body->message)->toBe('OK')
        ->and($response_body->errors)->toBeArray()
        ->and($response_body->errors)->toHaveCount(0);
});

test('can be instantiated with success response and invalid numbers', function () {
    $response_body = build_success_response_body_with_invalid_numbers();

    expect($response_body)->toBeInstanceOf(ResponseBody::class)
        ->and($response_body->success)->toBeTrue()
        ->and($response_body->statusCode)->toBe(200)
        ->and($response_body->message)->toBe('OK - Some numbers were invalid')
        ->and($response_body->payload->invalidToNumbers)->toHaveCount(3);
});

test('can be instantiated with bad request error', function () {
    $response_body = build_error_response_body_bad_request();

    expect($response_body)->toBeInstanceOf(ResponseBody::class)
        ->and($response_body->success)->toBeFalse()
        ->and($response_body->statusCode)->toBe(400)
        ->and($response_body->message)->toBe('Bad Request')
        ->and($response_body->errors)->toHaveCount(2)
        ->and($response_body->errors)->toContain('Invalid phone number format')
        ->and($response_body->errors)->toContain('Message body is required');
});

test('can be instantiated with unauthorized error', function () {
    $response_body = build_error_response_body_unauthorized();

    expect($response_body)->toBeInstanceOf(ResponseBody::class)
        ->and($response_body->success)->toBeFalse()
        ->and($response_body->statusCode)->toBe(401)
        ->and($response_body->message)->toBe('Unauthorized')
        ->and($response_body->errors)->toHaveCount(2)
        ->and($response_body->errors)->toContain('Invalid API key');
});

test('can be instantiated with server error', function () {
    $response_body = build_error_response_body_server_error();

    expect($response_body)->toBeInstanceOf(ResponseBody::class)
        ->and($response_body->success)->toBeFalse()
        ->and($response_body->statusCode)->toBe(500)
        ->and($response_body->message)->toBe('Internal Server Error')
        ->and($response_body->errors)->toHaveCount(2)
        ->and($response_body->errors)->toContain('Service temporarily unavailable');
});

test('can be instantiated with validation errors', function () {
    $response_body = build_error_response_body_validation_errors();

    expect($response_body)->toBeInstanceOf(ResponseBody::class)
        ->and($response_body->success)->toBeFalse()
        ->and($response_body->statusCode)->toBe(422)
        ->and($response_body->message)->toBe('Validation Error')
        ->and($response_body->errors)->toHaveCount(3)
        ->and($response_body->errors)->toContain('The body field is required.');
});

test('can be instantiated with rate limit error', function () {
    $response_body = build_error_response_body_rate_limited();

    expect($response_body)->toBeInstanceOf(ResponseBody::class)
        ->and($response_body->success)->toBeFalse()
        ->and($response_body->statusCode)->toBe(429)
        ->and($response_body->message)->toBe('Too Many Requests')
        ->and($response_body->errors)->toHaveCount(2)
        ->and($response_body->errors)->toContain('Rate limit exceeded');
});

test('toArray method works with success response', function () {
    $response_body = build_success_response_body();
    $array = $response_body->toArray();

    expect($array)->toBeArray()
        ->and($array)->toHaveKeys(['success', 'status_code', 'message', 'payload', 'errors'])
        ->and($array['success'])->toBeTrue()
        ->and($array['status_code'])->toBe(200)
        ->and($array['message'])->toBe('OK')
        ->and($array['payload'])->toBeArray()
        ->and($array['errors'])->toBeArray();
});

test('toArray method works with error response', function () {
    $response_body = build_error_response_body_bad_request();
    $array = $response_body->toArray();

    expect($array)->toBeArray()
        ->and($array)->toHaveKeys(['success', 'status_code', 'message', 'payload', 'errors'])
        ->and($array['success'])->toBeFalse()
        ->and($array['status_code'])->toBe(400)
        ->and($array['message'])->toBe('Bad Request')
        ->and($array['errors'])->toHaveCount(2);
});

test('payload is always present even in error responses', function () {
    $error_response = build_error_response_body_bad_request();

    expect($error_response->payload)->not->toBeNull()
        ->and($error_response->payload)->toBeInstanceOf(ResponsePayload::class);
});

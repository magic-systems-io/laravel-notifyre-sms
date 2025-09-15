<?php

use MagicSystemsIO\Notifyre\DTO\SMS\InvalidNumber;
use MagicSystemsIO\Notifyre\DTO\SMS\Metadata;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponseBody as ResponseBodyDto;
use MagicSystemsIO\Notifyre\Utils\ResponseParser;

it('returns null when given an empty response', function () {
    $result = ResponseParser::parseSmsResponse([], 200);

    expect($result)->toBeNull();
});

it('parses a full response array into a ResponseBody DTO', function () {
    $response = [
        'success' => true,
        'statusCode' => 200,
        'message' => 'OK',
        'payload' => [
            'id' => 'sms_1',
            'recipients' => [
                ['id' => 'r1', 'toNumber' => '+10000000001'],
                ['id' => 'r2', 'toNumber' => '+10000000002'],
            ],
            'invalidToNumbers' => [
                ['number' => '+000', 'message' => 'Invalid number'],
            ],
            'metadata' => [
                'requestingUserId' => 'user_x',
                'requestingUserEmail' => 'x@example.com',
            ],
        ],
    ];

    $result = ResponseParser::parseSmsResponse($response, 200);

    expect($result)->toBeInstanceOf(ResponseBodyDto::class)
        ->and($result->success)->toBeTrue()
        ->and($result->statusCode)->toBe(200)
        ->and($result->payload->id)->toBe('sms_1')
        ->and(count($result->payload->recipients))->toBe(2)
        ->and($result->payload->invalidToNumbers)->toHaveCount(1)
        ->and($result->payload->invalidToNumbers[0])->toBeInstanceOf(InvalidNumber::class)
        ->and($result->payload->invalidToNumbers[0]->number)->toBe('+000')
        ->and($result->payload->metadata)->toBeInstanceOf(Metadata::class)
        ->and($result->payload->metadata->requestingUserId)->toBe('user_x');
});

it('parses a direct payload (no top-level wrapper) and uses provided status code', function () {
    $payload = [
        'id' => 'sms_direct',
        'recipient' => ['id' => 'single', 'toNumber' => '+1999999999'],
    ];

    $result = ResponseParser::parseSmsResponse($payload, 202);

    expect($result)->toBeInstanceOf(ResponseBodyDto::class)
        ->and($result->success)->toBeTrue()
        ->and($result->statusCode)->toBe(202)
        ->and($result->payload->id)->toBe('sms_direct')
        ->and(count($result->payload->recipients))->toBe(1);
});

it('groups messages by id and merges recipients without duplicates', function () {
    $msgA = [
        'payload' => [
            'id' => 'group_1',
            'recipients' => [
                ['id' => 'r1', 'toNumber' => '+111'],
            ],
        ],
    ];

    $msgB = [
        'payload' => [
            'id' => 'group_1',
            'recipient' => ['id' => 'r2', 'toNumber' => '+222'],
        ],
    ];

    $msgC = [
        'payload' => [
            'id' => 'group_1',
            'recipients' => [
                ['id' => 'r1', 'toNumber' => '+111'],
                ['id' => 'r3', 'toNumber' => '+333'],
            ],
        ],
    ];

    $list = [$msgA, $msgB, $msgC];

    $results = ResponseParser::parseSmsListResponse($list, 200);

    expect(count($results))->toBe(1);

    $resp = $results[0];
    expect($resp)->toBeInstanceOf(ResponseBodyDto::class)
        ->and($resp->payload->id)->toBe('group_1');

    $ids = array_map(fn ($r) => $r->id, $resp->payload->recipients);
    sort($ids);
    expect($ids)->toBe(['r1', 'r2', 'r3']);
});

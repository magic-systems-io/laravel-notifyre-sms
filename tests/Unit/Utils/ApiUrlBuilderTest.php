<?php

use MagicSystemsIO\Notifyre\Utils\ApiUrlBuilder;

it('builds sms url without message id and handles trailing slash', function () {
    config(['notifyre.http.base_url' => 'https://api.example.com/']);

    $url = ApiUrlBuilder::buildSmsUrl();

    expect($url)->toBe('https://api.example.com/sms/send');
});

it('builds sms url with message id', function () {
    config(['notifyre.http.base_url' => 'https://api.example.com']);

    $url = ApiUrlBuilder::buildSmsUrl('abc123');

    expect($url)->toBe('https://api.example.com/sms/send/abc123');
});

it('throws when base url is not configured or is blank', function () {
    config(['notifyre.http.base_url' => null]);

    expect(fn () => ApiUrlBuilder::buildSmsUrl())->toThrow(InvalidArgumentException::class);

    config(['notifyre.http.base_url' => '   ']);

    expect(fn () => ApiUrlBuilder::buildSmsUrl())->toThrow(InvalidArgumentException::class);
});

it('returns base url unchanged when no query params provided', function () {
    $base = 'https://api.example.com/resource';

    expect(ApiUrlBuilder::buildUrlWithQuery($base))->toBe($base);
});

it('appends query string when params provided', function () {
    $base = 'https://api.example.com/resource';
    $params = ['a' => 1, 'b' => 'two'];

    $expected = $base . '?' . http_build_query($params);

    $result = ApiUrlBuilder::buildUrlWithQuery($base, $params);

    expect($result)->toBe($expected);
});

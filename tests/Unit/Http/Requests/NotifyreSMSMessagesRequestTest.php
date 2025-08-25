<?php

namespace MagicSystemsIO\Notifyre\Tests\Unit\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use MagicSystemsIO\Notifyre\Http\Requests\NotifyreSMSMessagesRequest;

test('can be instantiated', function () {
    $request = new NotifyreSMSMessagesRequest();

    expect($request)->toBeInstanceOf(NotifyreSMSMessagesRequest::class)
        ->and($request)->toBeInstanceOf(FormRequest::class);
});

test('authorize returns true', function () {
    $request = new NotifyreSMSMessagesRequest();

    expect($request->authorize())->toBeTrue();
});

test('validation rules are correct', function () {
    $request = new NotifyreSMSMessagesRequest();
    $rules = $request->rules();

    expect($rules)->toHaveKey('body')
        ->and($rules)->toHaveKey('sender')
        ->and($rules)->toHaveKey('recipients')
        ->and($rules)->toHaveKey('recipients.*')
        ->and($rules)->toHaveKey('recipients.*.type')
        ->and($rules)->toHaveKey('recipients.*.value')
        ->and($rules)->toHaveKey('persist');
});

test('body field is required and has correct validation', function () {
    $request = new NotifyreSMSMessagesRequest();
    $rules = $request->rules();

    expect($rules['body'])->toContain('required')
        ->and($rules['body'])->toContain('string')
        ->and($rules['body'])->toContain('max:160');
});

test('sender field is optional and has correct validation', function () {
    $request = new NotifyreSMSMessagesRequest();
    $rules = $request->rules();

    expect($rules['sender'])->toContain('nullable')
        ->and($rules['sender'])->toContain('string')
        ->and($rules['sender'])->toContain('max:255');
});

test('recipients field is required and has correct validation', function () {
    $request = new NotifyreSMSMessagesRequest();
    $rules = $request->rules();

    expect($rules['recipients'])->toContain('required')
        ->and($rules['recipients'])->toContain('array')
        ->and($rules['recipients'])->toContain('min:1');
});

test('recipients array elements are required and have correct validation', function () {
    $request = new NotifyreSMSMessagesRequest();
    $rules = $request->rules();

    expect($rules['recipients.*'])->toContain('required')
        ->and($rules['recipients.*'])->toContain('array')
        ->and($rules['recipients.*'])->toContain('min:1');
});

test('recipient type field is required and validates enum', function () {
    $request = new NotifyreSMSMessagesRequest();
    $rules = $request->rules();

    expect($rules['recipients.*.type'])->toContain('required')
        ->and($rules['recipients.*.type'])->toContain('string');

    // Check that it uses the Rule::enum validation
    $typeRule = collect($rules['recipients.*.type'])->first(function ($rule) {
        return is_object($rule);
    });

    expect($typeRule)->not->toBeNull();
});

test('recipient value field is required and has correct validation', function () {
    $request = new NotifyreSMSMessagesRequest();
    $rules = $request->rules();

    expect($rules['recipients.*.value'])->toContain('required')
        ->and($rules['recipients.*.value'])->toContain('string')
        ->and($rules['recipients.*.value'])->toContain('max:255');
});

test('persist field is optional and boolean', function () {
    $request = new NotifyreSMSMessagesRequest();
    $rules = $request->rules();

    expect($rules['persist'])->toContain('nullable')
        ->and($rules['persist'])->toContain('boolean');
});

test('validates valid request data structure', function () {
    $request = new NotifyreSMSMessagesRequest();
    $rules = $request->rules();

    // Test that all required validation rules are present
    expect($rules['body'])->toContain('required')
        ->and($rules['body'])->toContain('string')
        ->and($rules['body'])->toContain('max:160')
        ->and($rules['recipients'])->toContain('required')
        ->and($rules['recipients'])->toContain('array')
        ->and($rules['recipients'])->toContain('min:1');
});

test('sender field has correct validation rules', function () {
    $request = new NotifyreSMSMessagesRequest();
    $rules = $request->rules();

    expect($rules['sender'])->toContain('nullable')
        ->and($rules['sender'])->toContain('string')
        ->and($rules['sender'])->toContain('max:255');
});

test('recipients array validation rules are correct', function () {
    $request = new NotifyreSMSMessagesRequest();
    $rules = $request->rules();

    expect($rules['recipients'])->toContain('required')
        ->and($rules['recipients'])->toContain('array')
        ->and($rules['recipients'])->toContain('min:1')
        ->and($rules['recipients.*'])->toContain('required')
        ->and($rules['recipients.*'])->toContain('array')
        ->and($rules['recipients.*'])->toContain('min:1');
});

test('body field validation rules are correct', function () {
    $request = new NotifyreSMSMessagesRequest();
    $rules = $request->rules();

    expect($rules['body'])->toContain('required')
        ->and($rules['body'])->toContain('string')
        ->and($rules['body'])->toContain('max:160');
});

test('recipient type field validation rules are correct', function () {
    $request = new NotifyreSMSMessagesRequest();
    $rules = $request->rules();

    expect($rules['recipients.*.type'])->toContain('required')
        ->and($rules['recipients.*.type'])->toContain('string');
});

test('recipient value field validation rules are correct', function () {
    $request = new NotifyreSMSMessagesRequest();
    $rules = $request->rules();

    expect($rules['recipients.*.value'])->toContain('required')
        ->and($rules['recipients.*.value'])->toContain('string')
        ->and($rules['recipients.*.value'])->toContain('max:255');
});

test('persist field validation rules are correct', function () {
    $request = new NotifyreSMSMessagesRequest();
    $rules = $request->rules();

    expect($rules['persist'])->toContain('nullable')
        ->and($rules['persist'])->toContain('boolean');
});

test('validation rules structure is complete', function () {
    $request = new NotifyreSMSMessagesRequest();
    $rules = $request->rules();

    // Test that all expected validation rules are present
    expect($rules)->toHaveKey('body')
        ->and($rules)->toHaveKey('sender')
        ->and($rules)->toHaveKey('recipients')
        ->and($rules)->toHaveKey('recipients.*')
        ->and($rules)->toHaveKey('recipients.*.type')
        ->and($rules)->toHaveKey('recipients.*.value')
        ->and($rules)->toHaveKey('persist');
});

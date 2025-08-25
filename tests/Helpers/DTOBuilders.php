<?php

namespace MagicSystemsIO\Notifyre\Tests\Helpers;

use MagicSystemsIO\Notifyre\DTO\SMS\InvalidNumber;
use MagicSystemsIO\Notifyre\DTO\SMS\Recipient;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponseBody;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponsePayload;
use MagicSystemsIO\Notifyre\Enums\NotifyreRecipientTypes;

// ============================================================================
// RECIPIENT BUILDERS
// ============================================================================

function build_recipient_virtual_mobile(): Recipient
{
    return new Recipient(
        type: NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value,
        value: '+61412345678'
    );
}

function build_recipient_contact(): Recipient
{
    return new Recipient(
        type: NotifyreRecipientTypes::CONTACT->value,
        value: 'contact-123'
    );
}

function build_recipient_group(): Recipient
{
    return new Recipient(
        type: NotifyreRecipientTypes::GROUP->value,
        value: 'group-456'
    );
}

function build_recipients_multiple(): array
{
    return [
        build_recipient_virtual_mobile(),
        build_recipient_contact(),
        build_recipient_group(),
    ];
}

function build_recipients_single(): array
{
    return [build_recipient_virtual_mobile()];
}

// ============================================================================
// REQUEST BODY BUILDERS
// ============================================================================

function build_request_body_basic(): RequestBody
{
    return new RequestBody(
        body: 'Hello, this is a test SMS message!',
        recipients: build_recipients_single()
    );
}

function build_request_body_with_sender(): RequestBody
{
    return new RequestBody(
        body: 'Hello, this is a test SMS message!',
        recipients: build_recipients_single(),
        sender: '+61487654321'
    );
}

function build_request_body_multiple_recipients(): RequestBody
{
    return new RequestBody(
        body: 'Hello, this is a test SMS message for multiple recipients!',
        recipients: build_recipients_multiple()
    );
}

function build_request_body_long_message(): RequestBody
{
    return new RequestBody(
        body: str_repeat('This is a very long message that might exceed normal SMS length limits. ', 10),
        recipients: build_recipients_single()
    );
}

// ============================================================================
// INVALID NUMBER BUILDERS
// ============================================================================

function build_invalid_number_basic(): InvalidNumber
{
    return new InvalidNumber(
        number: '+1234567890',
        message: 'Invalid phone number format'
    );
}

function build_invalid_number_invalid_format(): InvalidNumber
{
    return new InvalidNumber(
        number: 'not-a-number',
        message: 'Phone number must contain only digits and valid characters'
    );
}

function build_invalid_number_too_short(): InvalidNumber
{
    return new InvalidNumber(
        number: '+123',
        message: 'Phone number too short'
    );
}

function build_invalid_numbers_multiple(): array
{
    return [
        build_invalid_number_basic(),
        build_invalid_number_invalid_format(),
        build_invalid_number_too_short(),
    ];
}

// ============================================================================
// RESPONSE PAYLOAD BUILDERS
// ============================================================================

function build_success_response_payload(): ResponsePayload
{
    return new ResponsePayload(
        smsMessageID: 'sms-msg-123456789',
        friendlyID: 'friendly-msg-001',
        invalidToNumbers: []
    );
}

function build_response_payload_with_invalid_numbers(): ResponsePayload
{
    return new ResponsePayload(
        smsMessageID: 'sms-msg-123456789',
        friendlyID: 'friendly-msg-002',
        invalidToNumbers: build_invalid_numbers_multiple()
    );
}

function build_response_payload_single_invalid_number(): ResponsePayload
{
    return new ResponsePayload(
        smsMessageID: 'sms-msg-123456789',
        friendlyID: 'friendly-msg-003',
        invalidToNumbers: [build_invalid_number_basic()]
    );
}

function build_response_payload_long_ids(): ResponsePayload
{
    return new ResponsePayload(
        smsMessageID: str_repeat('sms-msg-', 10) . '123456789',
        friendlyID: str_repeat('friendly-msg-', 10) . '001',
        invalidToNumbers: []
    );
}

// ============================================================================
// RESPONSE BODY BUILDERS
// ============================================================================

function build_success_response_body(): ResponseBody
{
    return new ResponseBody(
        success: true,
        statusCode: 200,
        message: 'OK',
        payload: build_success_response_payload(),
        errors: []
    );
}

function build_success_response_body_with_invalid_numbers(): ResponseBody
{
    return new ResponseBody(
        success: true,
        statusCode: 200,
        message: 'OK - Some numbers were invalid',
        payload: build_response_payload_with_invalid_numbers(),
        errors: []
    );
}

function build_error_response_body_bad_request(): ResponseBody
{
    return new ResponseBody(
        success: false,
        statusCode: 400,
        message: 'Bad Request',
        payload: build_success_response_payload(),
        errors: ['Invalid phone number format', 'Message body is required']
    );
}

function build_error_response_body_unauthorized(): ResponseBody
{
    return new ResponseBody(
        success: false,
        statusCode: 401,
        message: 'Unauthorized',
        payload: build_success_response_payload(),
        errors: ['Invalid API key', 'Authentication required']
    );
}

function build_error_response_body_server_error(): ResponseBody
{
    return new ResponseBody(
        success: false,
        statusCode: 500,
        message: 'Internal Server Error',
        payload: build_success_response_payload(),
        errors: ['Service temporarily unavailable', 'Please try again later']
    );
}

function build_error_response_body_validation_errors(): ResponseBody
{
    return new ResponseBody(
        success: false,
        statusCode: 422,
        message: 'Validation Error',
        payload: build_success_response_payload(),
        errors: [
            'The body field is required.',
            'The recipients field is required.',
            'The recipients.0.value field is required.',
        ]
    );
}

function build_error_response_body_rate_limited(): ResponseBody
{
    return new ResponseBody(
        success: false,
        statusCode: 429,
        message: 'Too Many Requests',
        payload: build_success_response_payload(),
        errors: ['Rate limit exceeded', 'Please wait before sending more messages']
    );
}

// ============================================================================
// EDGE CASE BUILDERS
// ============================================================================

function build_request_body_empty_recipients(): RequestBody
{
    return new RequestBody(
        body: 'This will cause an error',
        recipients: []
    );
}

function build_request_body_empty_body(): RequestBody
{
    return new RequestBody(
        body: '',
        recipients: build_recipients_single()
    );
}

function build_request_body_whitespace_body(): RequestBody
{
    return new RequestBody(
        body: '   ',
        recipients: build_recipients_single()
    );
}

function build_recipient_empty_value(): Recipient
{
    return new Recipient(
        type: NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value,
        value: ''
    );
}

function build_recipient_whitespace_value(): Recipient
{
    return new Recipient(
        type: NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value,
        value: '   '
    );
}

function build_invalid_number_empty_message(): InvalidNumber
{
    return new InvalidNumber(
        number: '+1234567890',
        message: ''
    );
}

function build_invalid_number_whitespace_message(): InvalidNumber
{
    return new InvalidNumber(
        number: '+1234567890',
        message: '   '
    );
}

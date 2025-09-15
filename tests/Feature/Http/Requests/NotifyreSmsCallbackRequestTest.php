<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponseBody;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponsePayload;
use MagicSystemsIO\Notifyre\DTO\SMS\SmsRecipient;
use MagicSystemsIO\Notifyre\Http\Requests\NotifyreSmsCallbackRequest;

uses(RefreshDatabase::class);

it('validates and converts a full callback payload to ResponseBody DTO', function () {
    $data = [
        'success' => true,
        'status_code' => 200,
        'message' => 'OK',
        'payload' => [
            'id' => 'payload_123',
            'friendly_id' => 'friendly_payload_123',
            'account_id' => 'account_123',
            'created_by' => 'user_123',
            'recipients' => [
                [
                    'id' => 'sms_123',
                    'friendly_id' => 'friendly_123',
                    'to_number' => '+12345678901',
                    'from_number' => '+09876543210',
                    'cost' => 0.05,
                    'message_parts' => 1,
                    'cost_per_part' => 0.05,
                    'status' => 'sent',
                    'status_message' => 'Message sent successfully',
                    'delivery_status' => null,
                    'queued_date_utc' => time(),
                    'completed_date_utc' => time() + 60,
                ],
            ],
            'status' => 'completed',
            'total_cost' => 0.05,
            'metadata' => [
                'requesting_user_id' => 'user123',
                'requesting_user_email' => 'test@example.com',
            ],
            'created_date_utc' => time(),
            'submitted_date_utc' => time() + 10,
            'completed_date_utc' => time() + 60,
            'last_modified_date_utc' => time() + 60,
            'campaign_name' => 'Test Campaign',
            'invalid_to_numbers' => [
                ['number' => '+0000000000', 'message' => 'Invalid phone number format'],
            ],
        ],
        'errors' => [],
    ];

    $request = new NotifyreSmsCallbackRequest();
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));

    $request->replace($data);
    $request->validateResolved();

    $dto = $request->toResponseBody();

    expect($dto)->toBeInstanceOf(ResponseBody::class)
        ->and($dto->payload)->toBeInstanceOf(ResponsePayload::class)
        ->and(count($dto->payload->recipients))->toBe(1)
        ->and($dto->payload->recipients[0])->toBeInstanceOf(SmsRecipient::class)
        ->and($dto->payload->id)->toBe('payload_123')
        ->and($dto->success)->toBeTrue();
});

it('throws a ValidationException when required fields are missing', function () {
    $data = [
        'success' => true,
        'status_code' => 200,
    ];

    $request = new NotifyreSmsCallbackRequest();
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));

    $request->replace($data);

    $this->expectException(ValidationException::class);

    $request->validateResolved();
});

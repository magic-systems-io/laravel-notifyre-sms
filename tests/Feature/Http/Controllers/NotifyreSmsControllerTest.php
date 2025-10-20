<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request as HttpRequest;
use MagicSystemsIO\Notifyre\Contracts\NotifyreManager;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
use MagicSystemsIO\Notifyre\DTO\SMS\SmsRecipient;
use MagicSystemsIO\Notifyre\Http\Controllers\NotifyreSmsController;
use MagicSystemsIO\Notifyre\Http\Requests\NotifyreSmsCallbackRequest;
use MagicSystemsIO\Notifyre\Models\JunctionTables\NotifyreSmsMessageRecipient;
use MagicSystemsIO\Notifyre\Models\NotifyreRecipients;
use MagicSystemsIO\Notifyre\Models\NotifyreSmsMessages;

uses(RefreshDatabase::class);

it('returns 201 when sending a message succeeds', function () {
    $mock = Mockery::mock(NotifyreManager::class);
    app()->instance(NotifyreManager::class, $mock);

    $mock->shouldReceive('send')->once()
        ->with(Mockery::on(function ($arg) {
            return $arg instanceof RequestBody
                && $arg->body === 'Hello'
                && is_array($arg->recipients)
                && count($arg->recipients) === 1
                && $arg->recipients[0]->value === '+12345678901';
        }))
        ->andReturnNull();

    $this->postJson('/notifyre/sms/messages', [
        'body' => 'Hello',
        'sender' => 'TestSender',
        'recipients' => [
            ['type' => 'mobile_number', 'value' => '+12345678901'],
        ],
    ])->assertStatus(201)
      ->assertExactJson(['Message is being sent']);
});

it('returns 500 when sending a message throws an exception', function () {
    $mock = Mockery::mock(NotifyreManager::class);
    app()->instance(NotifyreManager::class, $mock);

    $mock->shouldReceive('send')->once()->andThrow(new Exception('upstream-failure'));

    $this->postJson('/notifyre/sms/messages', [
        'body' => 'Hello',
        'sender' => 'TestSender',
        'recipients' => [['type' => 'mobile_number', 'value' => '+12345678901']],
    ])->assertStatus(500)
      ->assertJson(['message' => 'upstream-failure']);
});

it('returns JSON from get() on notifyre manager', function () {
    $mock = Mockery::mock(NotifyreManager::class);
    app()->instance(NotifyreManager::class, $mock);

    $responseBody = createResponseBody([
        'payload' => createResponsePayload(['id' => 'sms_1', 'status' => 'ok']),
    ]);

    $mock->shouldReceive('get')->with('sms_1')->once()->andReturn($responseBody);

    $this->getJson('/notifyre/sms/remote/sms_1')->assertStatus(200)->assertJson([
        'success' => true,
        'status_code' => 200,
        'message' => 'Success',
        'payload' => [
            'id' => 'sms_1',
            'status' => 'ok',
        ],
    ]);
});

it('returns JSON list from list() on notifyre manager', function () {
    $mock = Mockery::mock(NotifyreManager::class);
    app()->instance(NotifyreManager::class, $mock);

    $mock->shouldReceive('list')->once()->with([])->andReturn(['data' => [1, 2, 3]]);

    $this->getJson('/notifyre/sms/remote')->assertStatus(200)->assertJson(['data' => [1, 2, 3]]);
});

it('aborts with 503 when database functionality is disabled', function () {
    config()->set('notifyre.database.enabled', false);

    $this->postJson('/notifyre/sms/messages', [
        'body' => 'Hello',
        'sender' => 'TestSender',
        'recipients' => [['type' => 'mobile_number', 'value' => '+12345678901']],
    ])->assertStatus(503);
});

it('returns paginated messages for authenticated user', function () {
    $sender = 'AuthSender';
    NotifyreSmsMessages::factory()->count(20)->create(['sender' => $sender]);

    $user = new readonly class ($sender)
    {
        public function __construct(private string $sender)
        {
        }

        public function getSender(): string
        {
            return $this->sender;
        }
    };

    $request = HttpRequest::create('/notifyre/sms/messages');
    $request->setUserResolver(fn () => $user);

    $controller = new NotifyreSmsController();
    $response = $controller->indexMessages($request);

    expect($response->getStatusCode())->toBe(200);

    $data = $response->getData(true);
    expect($data)->toBeArray()
        ->and($data['items'])->toHaveCount(15)
        ->and($data['pagination']['perPage'])->toBe(15)
        ->and($data['pagination']['total'])->toBe(20);
});

it('returns 404 when showing a non-existent message', function () {
    $controller = new NotifyreSmsController();
    $response = $controller->showMessage('does-not-exist');

    expect($response->getStatusCode())->toBe(404)
        ->and($response->getData(true))->toBe(['error' => 'Message not found']);
});

it('returns message with recipients when showing an existing message', function () {
    $message = NotifyreSmsMessages::factory()->create(['sender' => 'S']);
    $recipient = NotifyreRecipients::factory()->create(['value' => '+19998887766', 'type' => 'mobile_number']);

    $message->recipients()->attach($recipient->id, ['sent' => false]);

    $controller = new NotifyreSmsController();
    $response = $controller->showMessage($message->id);

    expect($response->getStatusCode())->toBe(200);

    $json = $response->getData(true);
    expect($json['id'])->toBe($message->id)
        ->and($json['recipients'])->toBeArray()
        ->and(count($json['recipients']))->toBe(1);
});

it('returns recipient history when recipient exists', function () {
    $message = NotifyreSmsMessages::factory()->create(['sender' => 'S']);
    $recipient = NotifyreRecipients::factory()->create(['value' => '+17770001111', 'type' => 'mobile_number']);
    $message->recipients()->attach($recipient->id, ['sent' => true]);

    $controller = new NotifyreSmsController();
    $response = $controller->showMessagesSentToRecipient($recipient->id);

    expect($response->getStatusCode())->toBe(200);
    $data = $response->getData(true);
    expect($data['id'])->toBe($recipient->id);

    $collectionKey = array_key_exists('smsMessages', $data) ? 'smsMessages' : 'sms_messages';
    expect(array_key_exists($collectionKey, $data))->toBeTrue()
        ->and($data[$collectionKey])->toBeArray()->and(count($data[$collectionKey]))->toBe(1);
});

it('handles webhook callback and updates recipient id and sent status', function () {
    $message = NotifyreSmsMessages::factory()->create(['id' => 'sms_callback_1']);

    $recipient = NotifyreRecipients::factory()->create([
        'value' => '+14155550001',
        'type' => 'mobile_number',
    ]);

    $message->recipients()->attach($recipient->id, ['sent' => false]);

    [
        'Event' => 'sms_sent',
        'Timestamp' => time(),
        'Payload' => [
            'ID' => $message->id,
            'FriendlyID' => null,
            'AccountID' => 'acc_123',
            'CreatedBy' => 'user_456',
            'Status' => 'completed',
            'TotalCost' => 0.05,
            'CreatedDateUtc' => time() - 100,
            'SubmittedDateUtc' => time() - 50,
            'CompletedDateUtc' => time(),
            'LastModifiedDateUtc' => time(),
            'Recipient' => [
                'ID' => 'new_real_id_1',
                'ToNumber' => '+14155550001',
                'FromNumber' => '+15555551234',
                'Message' => null,
                'Cost' => 0.05,
                'MessageParts' => 1,
                'CostPerPart' => 0.05,
                'Status' => 'sent',
                'StatusMessage' => null,
                'DeliveryStatus' => 'delivered',
                'QueuedDateUtc' => time() - 50,
                'CompletedDateUtc' => time(),
            ],
            'Metadata' => null,
        ],
    ];

    $smsRecipient = new SmsRecipient(
        id: 'new_real_id_1',
        friendlyID: $message->id,
        toNumber: '+14155550001',
        fromNumber: '+15555551234',
        cost: 0.05,
        messageParts: 1,
        costPerPart: 0.05,
        status: 'sent',
        statusMessage: '',
        deliveryStatus: 'delivered',
        queuedDateUtc: time() - 50,
        completedDateUtc: time()
    );

    $mockRequest = Mockery::mock(NotifyreSmsCallbackRequest::class);
    $mockRequest->shouldReceive('validated')->with('Payload.ID')->once()->andReturn('sms_callback_1');
    $mockRequest->shouldReceive('getRecipient')->once()->andReturn($smsRecipient);

    $controller = new NotifyreSmsController();
    $response = $controller->handleCallback($mockRequest);

    expect($response->getStatusCode())->toBe(200);

    $updated = NotifyreRecipients::where('value', $recipient->value)->first();
    expect($updated)->not->toBeNull()
        ->and($updated->id)->toBe('new_real_id_1');

    $pivot = NotifyreSmsMessageRecipient::where('sms_message_id', $message->id)
        ->where('recipient_id', $updated->id)->first();

    expect($pivot)->not->toBeNull()
        ->and((bool) $pivot->sent)->toBeTrue();
});

it('matches recipient by value only in webhook callback', function () {
    $message = NotifyreSmsMessages::factory()->create(['id' => 'sms_match_test']);

    $recipient = NotifyreRecipients::factory()->create([
        'id' => 'temp_uuid_123',
        'value' => '+14155551111',
        'type' => 'mobile_number',
    ]);

    $message->recipients()->attach($recipient->id, ['sent' => false]);

    $smsRecipient = new SmsRecipient(
        id: 'real_notifyre_id_456',
        friendlyID: $message->id,
        toNumber: '+14155551111',
        fromNumber: '+15555551234',
        cost: 0.05,
        messageParts: 1,
        costPerPart: 0.05,
        status: 'sent',
        statusMessage: '',
        deliveryStatus: 'delivered',
        queuedDateUtc: time() - 50,
        completedDateUtc: time()
    );

    $mockRequest = Mockery::mock(NotifyreSmsCallbackRequest::class);
    $mockRequest->shouldReceive('validated')->with('Payload.ID')->once()->andReturn('sms_match_test');
    $mockRequest->shouldReceive('getRecipient')->once()->andReturn($smsRecipient);

    $controller = new NotifyreSmsController();
    $response = $controller->handleCallback($mockRequest);

    expect($response->getStatusCode())->toBe(200);

    $updated = NotifyreRecipients::where('value', '+14155551111')
        ->where('type', 'mobile_number')
        ->first();

    expect($updated)->not->toBeNull()
        ->and($updated->id)->toBe('real_notifyre_id_456')
        ->and($updated->value)->toBe('+14155551111')
        ->and($updated->type)->toBe('mobile_number');
});

it('handles multiple recipients with same value but different types', function () {
    $message = NotifyreSmsMessages::factory()->create(['id' => 'sms_same_value']);

    $mobileRecipient = NotifyreRecipients::factory()->create([
        'id' => 'mobile_temp_id',
        'value' => '+14155552222',
        'type' => 'mobile_number',
    ]);

    NotifyreRecipients::factory()->create([
        'id' => 'contact_temp_id',
        'value' => '+14155552222',
        'type' => 'contact',
    ]);

    $message->recipients()->attach($mobileRecipient->id, ['sent' => false]);

    $smsRecipient = new SmsRecipient(
        id: 'real_id_789',
        friendlyID: $message->id,
        toNumber: '+14155552222',
        fromNumber: '+15555551234',
        cost: 0.05,
        messageParts: 1,
        costPerPart: 0.05,
        status: 'sent',
        statusMessage: '',
        deliveryStatus: 'delivered',
        queuedDateUtc: time() - 50,
        completedDateUtc: time()
    );

    $mockRequest = Mockery::mock(NotifyreSmsCallbackRequest::class);
    $mockRequest->shouldReceive('validated')->with('Payload.ID')->once()->andReturn('sms_same_value');
    $mockRequest->shouldReceive('getRecipient')->once()->andReturn($smsRecipient);

    $controller = new NotifyreSmsController();
    $response = $controller->handleCallback($mockRequest);

    expect($response->getStatusCode())->toBe(200);

    $allRecipients = NotifyreRecipients::where('value', '+14155552222')->get();
    expect($allRecipients->count())->toBe(2);

    $updatedRecipient = NotifyreRecipients::find('real_id_789');
    expect($updatedRecipient)->not->toBeNull()
        ->and($updatedRecipient->value)->toBe('+14155552222');
});

it('handles multiple recipients with different values correctly', function () {
    $message = NotifyreSmsMessages::factory()->create(['id' => 'sms_multi_recipients']);

    $recipient1 = NotifyreRecipients::factory()->create([
        'id' => 'temp_id_001',
        'value' => '+14155553333',
        'type' => 'mobile_number',
    ]);

    $recipient2 = NotifyreRecipients::factory()->create([
        'id' => 'temp_id_002',
        'value' => '+14155554444',
        'type' => 'mobile_number',
    ]);

    $message->recipients()->attach($recipient1->id, ['sent' => false]);
    $message->recipients()->attach($recipient2->id, ['sent' => false]);

    $smsRecipient1 = new SmsRecipient(
        id: 'real_id_001',
        friendlyID: $message->id,
        toNumber: '+14155553333',
        fromNumber: '+15555551234',
        cost: 0.05,
        messageParts: 1,
        costPerPart: 0.05,
        status: 'sent',
        statusMessage: '',
        deliveryStatus: 'delivered',
        queuedDateUtc: time() - 50,
        completedDateUtc: time()
    );

    $mockRequest1 = Mockery::mock(NotifyreSmsCallbackRequest::class);
    $mockRequest1->shouldReceive('validated')->with('Payload.ID')->once()->andReturn('sms_multi_recipients');
    $mockRequest1->shouldReceive('getRecipient')->once()->andReturn($smsRecipient1);

    $controller = new NotifyreSmsController();
    $response1 = $controller->handleCallback($mockRequest1);

    expect($response1->getStatusCode())->toBe(200);

    $updated1 = NotifyreRecipients::where('value', '+14155553333')->first();
    expect($updated1)->not->toBeNull()
        ->and($updated1->id)->toBe('real_id_001');

    $unchanged = NotifyreRecipients::where('value', '+14155554444')->first();
    expect($unchanged)->not->toBeNull()
        ->and($unchanged->id)->toBe('temp_id_002');
});

it('returns 404 when recipient value does not exist in database', function () {
    $message = NotifyreSmsMessages::factory()->create(['id' => 'sms_no_recipient']);

    $smsRecipient = new SmsRecipient(
        id: 'real_id_999',
        friendlyID: $message->id,
        toNumber: '+14155559999',
        fromNumber: '+15555551234',
        cost: 0.05,
        messageParts: 1,
        costPerPart: 0.05,
        status: 'sent',
        statusMessage: '',
        deliveryStatus: 'delivered',
        queuedDateUtc: time() - 50,
        completedDateUtc: time()
    );

    $mockRequest = Mockery::mock(NotifyreSmsCallbackRequest::class);
    $mockRequest->shouldReceive('validated')->with('Payload.ID')->once()->andReturn('sms_no_recipient');
    $mockRequest->shouldReceive('getRecipient')->once()->andReturn($smsRecipient);

    $controller = new NotifyreSmsController();
    $response = $controller->handleCallback($mockRequest);

    expect($response->getStatusCode())->toBe(404)
        ->and($response->getData(true))->toBe(['message' => 'Recipient not found']);
});

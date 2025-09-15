<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request as HttpRequest;
use MagicSystemsIO\Notifyre\Contracts\NotifyreManager;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
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

    $this->postJson('/notifyre/sms', [
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

    $this->postJson('/notifyre/sms', [
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

    $this->getJson('/notifyre/sms/notifyre/sms_1')->assertStatus(200)->assertJson([
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

    $this->getJson('/notifyre/sms/notifyre')->assertStatus(200)->assertJson(['data' => [1, 2, 3]]);
});

it('aborts with 503 when database functionality is disabled', function () {
    config()->set('notifyre.database.enabled', false);

    $this->postJson('/notifyre/sms', [
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

    $request = HttpRequest::create('/notifyre/sms', 'GET');
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
        'tmp_id' => 'tmp_abc_1',
        'value' => '+14155550001',
        'type' => 'mobile_number',
    ]);

    $message->recipients()->attach($recipient->id, ['sent' => false]);

    $smsRecipient = createSmsRecipient([
        'id' => 'new_real_id_1',
        'toNumber' => '+14155550001',
        'status' => 'sent',
    ]);

    $responseBody = createResponseBody([
        'payload' => createResponsePayload([
            'id' => $message->id,
            'recipients' => [$smsRecipient],
            'invalidToNumbers' => [],
        ]),
    ]);

    $mockRequest = Mockery::mock(NotifyreSmsCallbackRequest::class);
    $mockRequest->shouldReceive('toResponseBody')->once()->andReturn($responseBody);

    $controller = new NotifyreSmsController();
    $response = $controller->handleWebhook($mockRequest);

    expect($response->getStatusCode())->toBe(200);

    $updated = NotifyreRecipients::where('value', $recipient->value)->first();
    expect($updated)->not->toBeNull()
        ->and($updated->id)->toBe('new_real_id_1');

    $pivot = NotifyreSmsMessageRecipient::where('sms_message_id', $message->id)
        ->where('recipient_id', $updated->id)->first();

    expect($pivot)->not->toBeNull()
        ->and((bool) $pivot->sent)->toBeTrue();
});

<?php

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use MagicSystemsIO\Notifyre\Models\JunctionTables\NotifyreSmsMessageRecipient;

uses(RefreshDatabase::class);

it('has a working factory and persists to the database', function () {
    $junction = NotifyreSmsMessageRecipient::factory()->create();

    $this->assertDatabaseHas('notifyre_sms_message_recipients', [
        'sms_message_id' => $junction->sms_message_id,
        'recipient_id' => $junction->recipient_id,
        'sent' => $junction->sent,
    ]);
});

it('casts sent to boolean', function () {
    $junction = NotifyreSmsMessageRecipient::factory()->create(['sent' => 1]);

    expect(is_bool($junction->sent))->toBeTrue()
        ->and($junction->sent)->toBeTrue();
});

it('has no primary key and timestamps', function () {
    $junction = NotifyreSmsMessageRecipient::factory()->create();

    expect($junction->getKey())->toBeNull()
        ->and($junction->timestamps)->toBeFalse();
});

it('enforces unique composite key and throws on duplicate', function () {
    $first = NotifyreSmsMessageRecipient::factory()->create();

    $this->expectException(QueryException::class);
    NotifyreSmsMessageRecipient::factory()->create([
        'sms_message_id' => $first->sms_message_id,
        'recipient_id' => $first->recipient_id,
    ]);
});

it('creates related message and recipient records via factory', function () {
    $junction = NotifyreSmsMessageRecipient::factory()->create();

    $this->assertDatabaseHas('notifyre_sms_messages', ['id' => $junction->sms_message_id]);
    $this->assertDatabaseHas('notifyre_recipients', ['id' => $junction->recipient_id]);
});

<?php

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use MagicSystemsIO\Notifyre\Models\NotifyreSmsMessages;

uses(RefreshDatabase::class);

it('has a working factory and persists to the database', function () {
    $message = NotifyreSmsMessages::factory()->create();

    $this->assertDatabaseHas('notifyre_sms_messages', [
        'id' => $message->id,
        'body' => $message->body,
    ]);
});

it('hides driver when converted to array or json', function () {
    $message = NotifyreSmsMessages::factory()->create(['driver' => 'sms']);

    $array = $message->toArray();
    expect($array)->not->toHaveKey('driver');
});

it('uses string primary key and is not incrementing', function () {
    $message = NotifyreSmsMessages::factory()->create();

    expect($message->getKeyType())->toBe('string')
        ->and($message->getIncrementing())->toBeFalse();
});

it('defines recipients relation as a BelongsToMany with pivot field delivery_status', function () {
    $message = NotifyreSmsMessages::factory()->create();

    $relation = $message->recipients();
    expect($relation)->toBeInstanceOf(BelongsToMany::class);

    $pivot = $relation->getPivotColumns();
    expect(in_array('delivery_status', $pivot))->toBeTrue();
});

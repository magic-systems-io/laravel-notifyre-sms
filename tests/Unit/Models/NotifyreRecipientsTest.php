<?php

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use MagicSystemsIO\Notifyre\Models\NotifyreRecipients;

uses(RefreshDatabase::class);

it('has a working factory and persists to the database', function () {
    $recipient = NotifyreRecipients::factory()->create();

    $this->assertDatabaseHas('notifyre_recipients', [
        'id' => $recipient->id,
        'value' => $recipient->value,
    ]);
});

it('uses string primary key and is not incrementing', function () {
    $recipient = NotifyreRecipients::factory()->create();

    expect($recipient->getKeyType())->toBe('string')
        ->and($recipient->getIncrementing())->toBeFalse();
});

it('defines smsMessages relation as a BelongsToMany', function () {
    $recipient = NotifyreRecipients::factory()->create();

    $relation = $recipient->smsMessages();
    expect($relation)->toBeInstanceOf(BelongsToMany::class);
});

<?php

namespace MagicSystemsIO\Notifyre\Tests\Unit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MagicSystemsIO\Notifyre\Models\JunctionTables\NotifyreSMSMessageRecipient;
use MagicSystemsIO\Notifyre\Models\NotifyreSMSMessages;

test('can be instantiated', function () {
    $model = new NotifyreSMSMessages();

    expect($model)->toBeInstanceOf(NotifyreSMSMessages::class)
        ->and($model)->toBeInstanceOf(Model::class);
});

test('uses HasFactory trait', function () {
    $model = new NotifyreSMSMessages();

    expect(method_exists($model, 'factory'))->toBeTrue();
});

test('has correct table name', function () {
    $model = new NotifyreSMSMessages();

    expect($model->getTable())->toBe('notifyre_sms_messages');
});

test('has correct fillable attributes', function () {
    $model = new NotifyreSMSMessages();

    expect($model->getFillable())->toBe([
        'messageId',
        'sender',
        'body',
    ]);
});

test('can be created with specific attributes', function () {
    $attributes = [
        'messageId' => 'test-message-123',
        'sender' => '+61487654321',
        'body' => 'Test SMS message',
    ];

    $model = NotifyreSMSMessages::create($attributes);

    expect($model->messageId)->toBe('test-message-123')
        ->and($model->sender)->toBe('+61487654321')
        ->and($model->body)->toBe('Test SMS message');
});

test('has messageRecipients relationship', function () {
    $model = new NotifyreSMSMessages();

    expect($model->messageRecipients())->toBeInstanceOf(HasMany::class);
});

test('messageRecipients relationship returns correct class', function () {
    $model = new NotifyreSMSMessages();
    $relationship = $model->messageRecipients();

    expect($relationship->getRelated())->toBeInstanceOf(NotifyreSMSMessageRecipient::class);
});

test('messageRecipients relationship uses correct foreign key', function () {
    $model = new NotifyreSMSMessages();
    $relationship = $model->messageRecipients();

    expect($relationship->getForeignKeyName())->toBe('sms_message_id');
});

test('can create message with null sender', function () {
    $model = NotifyreSMSMessages::create([
        'messageId' => 'test-message-123',
        'sender' => null,
        'body' => 'Test SMS message',
    ]);

    expect($model->sender)->toBeNull();
});

test('can create message with empty string sender', function () {
    $model = NotifyreSMSMessages::create([
        'messageId' => 'test-message-123',
        'sender' => '',
        'body' => 'Test SMS message',
    ]);

    expect($model->sender)->toBe('');
});

test('can create message with long body', function () {
    $longBody = str_repeat('This is a very long message. ', 10);

    $model = NotifyreSMSMessages::create([
        'messageId' => 'test-message-123',
        'sender' => '+61487654321',
        'body' => $longBody,
    ]);

    expect($model->body)->toBe($longBody);
});

test('can create message with special characters in body', function () {
    $specialBody = 'Message with special chars: @#$%^&*()_+-=[]{}|;:,.<>?';

    $model = NotifyreSMSMessages::create([
        'messageId' => 'test-message-123',
        'sender' => '+61487654321',
        'body' => $specialBody,
    ]);

    expect($model->body)->toBe($specialBody);
});

test('can create message with international phone number sender', function () {
    $model = NotifyreSMSMessages::create([
        'messageId' => 'test-message-123',
        'sender' => '+1-555-123-4567',
        'body' => 'Test SMS message',
    ]);

    expect($model->sender)->toBe('+1-555-123-4567');
});

test('can create message with alphanumeric sender', function () {
    $model = NotifyreSMSMessages::create([
        'messageId' => 'test-message-123',
        'sender' => 'SMS-Service-01',
        'body' => 'Test SMS message',
    ]);

    expect($model->sender)->toBe('SMS-Service-01');
});

test('can create message with UUID messageId', function () {
    $uuid = '550e8400-e29b-41d4-a716-446655440000';

    $model = NotifyreSMSMessages::create([
        'messageId' => $uuid,
        'sender' => '+61487654321',
        'body' => 'Test SMS message',
    ]);

    expect($model->messageId)->toBe($uuid);
});

test('can create message with alphanumeric messageId', function () {
    $model = NotifyreSMSMessages::create([
        'messageId' => 'MSG-2024-001',
        'sender' => '+61487654321',
        'body' => 'Test SMS message',
    ]);

    expect($model->messageId)->toBe('MSG-2024-001');
});

test('can create message with newlines in body', function () {
    $bodyWithNewlines = "Line 1\nLine 2\nLine 3";

    $model = NotifyreSMSMessages::create([
        'messageId' => 'test-message-123',
        'sender' => '+61487654321',
        'body' => $bodyWithNewlines,
    ]);

    expect($model->body)->toBe($bodyWithNewlines);
});

test('can create message with tabs in body', function () {
    $bodyWithTabs = "Column1\tColumn2\tColumn3";

    $model = NotifyreSMSMessages::create([
        'messageId' => 'test-message-123',
        'sender' => '+61487654321',
        'body' => $bodyWithTabs,
    ]);

    expect($model->body)->toBe($bodyWithTabs);
});

test('can create message with emojis in body', function () {
    $bodyWithEmojis = 'Hello! 👋 How are you? 😊';

    $model = NotifyreSMSMessages::create([
        'messageId' => 'test-message-123',
        'sender' => '+61487654321',
        'body' => $bodyWithEmojis,
    ]);

    expect($model->body)->toBe($bodyWithEmojis);
});

test('can create message with unicode characters in body', function () {
    $bodyWithUnicode = 'Café résumé naïve';

    $model = NotifyreSMSMessages::create([
        'messageId' => 'test-message-123',
        'sender' => '+61487654321',
        'body' => $bodyWithUnicode,
    ]);

    expect($model->body)->toBe($bodyWithUnicode);
});

test('can create message with very long messageId', function () {
    $longMessageId = str_repeat('A', 100);

    $model = NotifyreSMSMessages::create([
        'messageId' => $longMessageId,
        'sender' => '+61487654321',
        'body' => 'Test SMS message',
    ]);

    expect($model->messageId)->toBe($longMessageId);
});

test('can create message with very long sender', function () {
    $longSender = str_repeat('A', 100);

    $model = NotifyreSMSMessages::create([
        'messageId' => 'test-message-123',
        'sender' => $longSender,
        'body' => 'Test SMS message',
    ]);

    expect($model->sender)->toBe($longSender);
});

test('can create message with very long body', function () {
    $longBody = str_repeat('A', 1000);

    $model = NotifyreSMSMessages::create([
        'messageId' => 'test-message-123',
        'sender' => '+61487654321',
        'body' => $longBody,
    ]);

    expect($model->body)->toBe($longBody);
});

test('can be found by messageId', function () {
    $messageId = 'unique-message-123';

    NotifyreSMSMessages::create([
        'messageId' => $messageId,
        'sender' => '+61487654321',
        'body' => 'Test SMS message',
    ]);

    $foundModel = NotifyreSMSMessages::where('messageId', $messageId)->first();

    expect($foundModel)->not->toBeNull()
        ->and($foundModel->messageId)->toBe($messageId);
});

test('can be found by sender', function () {
    $sender = '+61487654321';

    NotifyreSMSMessages::create([
        'messageId' => 'test-message-123',
        'sender' => $sender,
        'body' => 'Test SMS message',
    ]);

    $foundModels = NotifyreSMSMessages::where('sender', $sender)->get();

    expect($foundModels)->toHaveCount(1)
        ->and($foundModels->first()->sender)->toBe($sender);
});

test('can be found by body content', function () {
    $body = 'Unique message content for testing';

    NotifyreSMSMessages::create([
        'messageId' => 'test-message-123',
        'sender' => '+61487654321',
        'body' => $body,
    ]);

    $foundModels = NotifyreSMSMessages::where('body', 'like', '%Unique message content%')->get();

    expect($foundModels)->toHaveCount(1)
        ->and($foundModels->first()->body)->toBe($body);
});

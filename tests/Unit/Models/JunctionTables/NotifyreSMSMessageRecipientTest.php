<?php

namespace MagicSystemsIO\Notifyre\Tests\Unit\Models\JunctionTables;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MagicSystemsIO\Notifyre\Models\JunctionTables\NotifyreSMSMessageRecipient;
use MagicSystemsIO\Notifyre\Models\NotifyreRecipients;
use MagicSystemsIO\Notifyre\Models\NotifyreSMSMessages;

describe('NotifyreSMSMessageRecipient', function () {
    it('has correct table name', function () {
        $model = new NotifyreSMSMessageRecipient();

        expect($model->getTable())->toBe('notifyre_sms_message_recipients');
    });

    it('has correct fillable attributes', function () {
        $model = new NotifyreSMSMessageRecipient();

        expect($model->getFillable())->toBe([
            'notifyre_sms_message_id',
            'notifyre_recipient_id',
        ]);
    });

    it('has correct casts', function () {
        $model = new NotifyreSMSMessageRecipient();

        expect($model->getCasts())->toHaveKey('notifyre_sms_message_id')
            ->and($model->getCasts())->toHaveKey('notifyre_recipient_id')
            ->and($model->getCasts()['notifyre_sms_message_id'])->toBe('integer')
            ->and($model->getCasts()['notifyre_recipient_id'])->toBe('integer');
    });

    it('does not use timestamps', function () {
        $model = new NotifyreSMSMessageRecipient();

        expect($model->usesTimestamps())->toBeFalse();
    });

    it('has recipient relationship', function () {
        $model = new NotifyreSMSMessageRecipient();

        $relationship = $model->recipient();

        expect($relationship)->toBeInstanceOf(BelongsTo::class)
            ->and($relationship->getRelated())->toBeInstanceOf(NotifyreRecipients::class)
            ->and($relationship->getForeignKeyName())->toBe('notifyre_recipient_id');
    });

    it('has message relationship', function () {
        $model = new NotifyreSMSMessageRecipient();

        $relationship = $model->message();

        expect($relationship)->toBeInstanceOf(BelongsTo::class)
            ->and($relationship->getRelated())->toBeInstanceOf(NotifyreSMSMessages::class)
            ->and($relationship->getForeignKeyName())->toBe('notifyre_sms_message_id');
    });

    it('can be instantiated', function () {
        $model = new NotifyreSMSMessageRecipient();

        expect($model)->toBeInstanceOf(NotifyreSMSMessageRecipient::class);
    });

    it('extends Eloquent Model', function () {
        $model = new NotifyreSMSMessageRecipient();

        expect($model)->toBeInstanceOf(Model::class);
    });

    it('has recipient method', function () {
        $model = new NotifyreSMSMessageRecipient();

        expect(method_exists($model, 'recipient'))->toBeTrue();
    });

    it('has message method', function () {
        $model = new NotifyreSMSMessageRecipient();

        expect(method_exists($model, 'message'))->toBeTrue();
    });

    it('recipient method returns BelongsTo relationship', function () {
        $model = new NotifyreSMSMessageRecipient();

        $relationship = $model->recipient();

        expect($relationship)->toBeInstanceOf(BelongsTo::class);
    });

    it('message method returns BelongsTo relationship', function () {
        $model = new NotifyreSMSMessageRecipient();

        $relationship = $model->message();

        expect($relationship)->toBeInstanceOf(BelongsTo::class);
    });

    it('has accessible properties', function () {
        $model = new NotifyreSMSMessageRecipient();

        $model->notifyre_sms_message_id = 1;
        $model->notifyre_recipient_id = 2;

        expect($model->notifyre_sms_message_id)->toBe(1)
            ->and($model->notifyre_recipient_id)->toBe(2);
    });
});

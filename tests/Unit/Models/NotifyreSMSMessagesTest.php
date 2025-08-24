<?php

namespace MagicSystemsIO\Notifyre\Tests\Unit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MagicSystemsIO\Notifyre\Models\JunctionTables\NotifyreSMSMessageRecipient;
use MagicSystemsIO\Notifyre\Models\NotifyreSMSMessages;

describe('NotifyreSMSMessages', function () {
    it('has correct table name', function () {
        $model = new NotifyreSMSMessages();

        expect($model->getTable())->toBe('notifyre_sms_messages');
    });

    it('has correct fillable attributes', function () {
        $model = new NotifyreSMSMessages();

        expect($model->getFillable())->toBe([
            'sender',
            'body',
        ]);
    });

    it('uses HasFactory trait', function () {
        $model = new NotifyreSMSMessages();

        expect(method_exists($model, 'factory'))->toBeTrue();
    });

    it('has messageRecipients relationship', function () {
        $model = new NotifyreSMSMessages();

        $relationship = $model->messageRecipients();

        expect($relationship)->toBeInstanceOf(HasMany::class)
            ->and($relationship->getRelated())->toBeInstanceOf(NotifyreSMSMessageRecipient::class)
            ->and($relationship->getForeignKeyName())->toBe('notifyre_sms_message_id');
    });

    it('can be instantiated', function () {
        $model = new NotifyreSMSMessages();

        expect($model)->toBeInstanceOf(NotifyreSMSMessages::class);
    });

    it('extends Eloquent Model', function () {
        $model = new NotifyreSMSMessages();

        expect($model)->toBeInstanceOf(Model::class);
    });

    it('has messageRecipients method', function () {
        $model = new NotifyreSMSMessages();

        expect(method_exists($model, 'messageRecipients'))->toBeTrue();
    });

    it('messageRecipients method returns HasMany relationship', function () {
        $model = new NotifyreSMSMessages();

        $relationship = $model->messageRecipients();

        expect($relationship)->toBeInstanceOf(HasMany::class);
    });

    it('messageRecipients relationship has correct foreign key', function () {
        $model = new NotifyreSMSMessages();

        $relationship = $model->messageRecipients();

        expect($relationship->getForeignKeyName())->toBe('notifyre_sms_message_id');
    });

    it('messageRecipients relationship points to correct model', function () {
        $model = new NotifyreSMSMessages();

        $relationship = $model->messageRecipients();

        expect($relationship->getRelated())->toBeInstanceOf(NotifyreSMSMessageRecipient::class);
    });
});

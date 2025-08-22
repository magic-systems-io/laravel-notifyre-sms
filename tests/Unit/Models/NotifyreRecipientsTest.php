<?php

namespace Arbi\Notifyre\Tests\Unit\Models;

use Arbi\Notifyre\Models\JunctionTables\NotifyreSMSMessageRecipient;
use Arbi\Notifyre\Models\NotifyreRecipients;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

describe('NotifyreRecipients', function () {
    it('has correct fillable attributes', function () {
        $model = new NotifyreRecipients();

        expect($model->getFillable())->toBe([
            'type',
            'value',
        ]);
    });

    it('has correct casts', function () {
        $model = new NotifyreRecipients();

        expect($model->getCasts())->toHaveKey('type')
            ->and($model->getCasts())->toHaveKey('value')
            ->and($model->getCasts()['type'])->toBe('string')
            ->and($model->getCasts()['value'])->toBe('string');
    });

    it('has notifyreSMSMessageRecipients relationship', function () {
        $model = new NotifyreRecipients();

        $relationship = $model->notifyreSMSMessageRecipients();

        expect($relationship)->toBeInstanceOf(HasMany::class)
            ->and($relationship->getRelated())->toBeInstanceOf(NotifyreSMSMessageRecipient::class)
            ->and($relationship->getForeignKeyName())->toBe('notifyre_recipient_id');
    });

    it('can be instantiated', function () {
        $model = new NotifyreRecipients();

        expect($model)->toBeInstanceOf(NotifyreRecipients::class);
    });

    it('extends Eloquent Model', function () {
        $model = new NotifyreRecipients();

        expect($model)->toBeInstanceOf(Model::class);
    });

    it('has notifyreSMSMessageRecipients method', function () {
        $model = new NotifyreRecipients();

        expect(method_exists($model, 'notifyreSMSMessageRecipients'))->toBeTrue();
    });

    it('notifyreSMSMessageRecipients method returns HasMany relationship', function () {
        $model = new NotifyreRecipients();

        $relationship = $model->notifyreSMSMessageRecipients();

        expect($relationship)->toBeInstanceOf(HasMany::class);
    });

    it('notifyreSMSMessageRecipients relationship has correct foreign key', function () {
        $model = new NotifyreRecipients();

        $relationship = $model->notifyreSMSMessageRecipients();

        expect($relationship->getForeignKeyName())->toBe('notifyre_recipient_id');
    });

    it('notifyreSMSMessageRecipients relationship points to correct model', function () {
        $model = new NotifyreRecipients();

        $relationship = $model->notifyreSMSMessageRecipients();

        expect($relationship->getRelated())->toBeInstanceOf(NotifyreSMSMessageRecipient::class);
    });

    it('has type and value properties accessible', function () {
        $model = new NotifyreRecipients();

        $model->type = 'virtual_mobile_number';
        $model->value = '+1234567890';

        expect($model->type)->toBe('virtual_mobile_number')
            ->and($model->value)->toBe('+1234567890');
    });
});

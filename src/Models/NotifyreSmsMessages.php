<?php

namespace MagicSystemsIO\Notifyre\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use MagicSystemsIO\Notifyre\Models\JunctionTables\NotifyreSmsMessageRecipient;

class NotifyreSmsMessages extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'sender',
        'body',
        'driver',
    ];

    public function messageRecipients(): HasMany
    {
        return $this->hasMany(NotifyreSmsMessageRecipient::class, 'sms_message_id');
    }

    public function recipients(): HasManyThrough
    {
        return $this->hasManyThrough(
            NotifyreRecipients::class,
            NotifyreSmsMessageRecipient::class,
            'sms_message_id',
            'id',
            'id',
            'recipient_id'
        );
    }
}

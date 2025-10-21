<?php

namespace MagicSystemsIO\Notifyre\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use MagicSystemsIO\Notifyre\Database\Factories\NotifyreSmsMessagesFactory;
use MagicSystemsIO\Notifyre\Models\JunctionTables\NotifyreSmsMessageRecipient;

class NotifyreSmsMessages extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'sender',
        'body',
        'driver',
    ];

    protected $casts = [
        'id' => 'string',
        'sender' => 'string',
        'body' => 'string',
        'driver' => 'string',
    ];

    protected $hidden = [
        'driver',
    ];

    protected static function newFactory(): NotifyreSmsMessagesFactory
    {
        return NotifyreSmsMessagesFactory::new();
    }

    public function recipients(): BelongsToMany
    {
        return $this->belongsToMany(
            NotifyreRecipients::class,
            NotifyreSmsMessageRecipient::class,
            'sms_message_id',
            'recipient_id'
        )->withPivot('delivery_status');
    }
}

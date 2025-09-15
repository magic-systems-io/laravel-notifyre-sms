<?php

namespace MagicSystemsIO\Notifyre\Models\JunctionTables;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MagicSystemsIO\Notifyre\Database\Factories\Junction\NotifyreSmsMessageRecipientFactory;

class NotifyreSmsMessageRecipient extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $incrementing = false;

    protected $primaryKey = null;

    protected $fillable = [
        'sms_message_id',
        'recipient_id',
        'sent',
    ];

    protected $casts = [
        'sms_message_id' => 'string',
        'recipient_id' => 'string',
        'sent' => 'boolean',
    ];

    protected static function newFactory(): NotifyreSmsMessageRecipientFactory
    {
        return NotifyreSmsMessageRecipientFactory::new();
    }
}

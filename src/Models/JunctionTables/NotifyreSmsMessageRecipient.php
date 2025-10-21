<?php

namespace MagicSystemsIO\Notifyre\Models\JunctionTables;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MagicSystemsIO\Notifyre\Database\Factories\Junction\NotifyreSmsMessageRecipientFactory;
use MagicSystemsIO\Notifyre\Models\Traits\HasConfigurableKey;

class NotifyreSmsMessageRecipient extends Model
{
    use HasFactory;
    use HasConfigurableKey;

    public $timestamps = false;

    protected $fillable = [
        'sms_message_id',
        'recipient_id',
        'delivery_status',
    ];

    protected $casts = [
        'sms_message_id' => 'string',
        'recipient_id' => 'string',
        'delivery_status' => 'string',
    ];

    protected static function newFactory(): NotifyreSmsMessageRecipientFactory
    {
        return NotifyreSmsMessageRecipientFactory::new();
    }
}

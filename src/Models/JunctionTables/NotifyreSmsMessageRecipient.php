<?php

namespace MagicSystemsIO\Notifyre\Models\JunctionTables;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotifyreSmsMessageRecipient extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $keyType = 'string';

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
}

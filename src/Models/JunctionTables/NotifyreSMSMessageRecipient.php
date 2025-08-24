<?php

namespace MagicSystemsIO\Notifyre\Models\JunctionTables;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MagicSystemsIO\Notifyre\Models\NotifyreRecipients;
use MagicSystemsIO\Notifyre\Models\NotifyreSMSMessages;

class NotifyreSMSMessageRecipient extends Model
{
    public $timestamps = false;

    protected $table = 'notifyre_sms_message_recipients';

    protected $fillable = [
        'notifyre_sms_message_id',
        'notifyre_recipient_id',
    ];

    protected $casts = [
        'notifyre_sms_message_id' => 'integer',
        'notifyre_recipient_id' => 'integer',
    ];

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(NotifyreRecipients::class, 'notifyre_recipient_id');
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(NotifyreSMSMessages::class, 'notifyre_sms_message_id');
    }
}

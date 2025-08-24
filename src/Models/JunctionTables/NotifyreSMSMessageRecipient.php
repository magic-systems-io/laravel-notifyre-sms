<?php

namespace MagicSystemsIO\Notifyre\Models\JunctionTables;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MagicSystemsIO\Notifyre\Models\NotifyreRecipients;
use MagicSystemsIO\Notifyre\Models\NotifyreSMSMessages;

class NotifyreSMSMessageRecipient extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'notifyre_sms_message_recipients';

    protected $fillable = [
        'sms_message_id',
        'recipient_id',
    ];

    protected $casts = [
        'sms_message_id' => 'integer',
        'recipient_id' => 'integer',
    ];

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(NotifyreRecipients::class, 'recipient_id');
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(NotifyreSMSMessages::class, 'sms_message_id');
    }
}

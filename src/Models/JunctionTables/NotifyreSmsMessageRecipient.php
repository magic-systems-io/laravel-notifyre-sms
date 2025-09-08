<?php

namespace MagicSystemsIO\Notifyre\Models\JunctionTables;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MagicSystemsIO\Notifyre\Models\NotifyreRecipients;
use MagicSystemsIO\Notifyre\Models\NotifyreSmsMessages;

class NotifyreSmsMessageRecipient extends Model
{
    use HasFactory;

    public $timestamps = false;

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

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(NotifyreRecipients::class, 'recipient_id');
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(NotifyreSmsMessages::class, 'sms_message_id');
    }
}

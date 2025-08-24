<?php

namespace MagicSystemsIO\Notifyre\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MagicSystemsIO\Notifyre\Models\JunctionTables\NotifyreSMSMessageRecipient;

class NotifyreSMSMessages extends Model
{
    use HasFactory;

    protected $table = 'notifyre_sms_messages';

    protected $fillable = [
        'messageId',
        'sender',
        'body',
    ];

    public function messageRecipients(): HasMany
    {
        return $this->hasMany(NotifyreSMSMessageRecipient::class, 'notifyre_sms_message_id');
    }
}

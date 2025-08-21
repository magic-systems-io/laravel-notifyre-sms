<?php

namespace Arbi\Notifyre\Models;

use Arbi\Notifyre\Models\JunctionTables\NotifyreSMSMessageRecipient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotifyreSMSMessages extends Model
{
    use HasFactory;

    protected $table = 'notifyre_sms_messages';

    protected $fillable = [
        'sender',
        'body',
    ];

    public function messageRecipients(): HasMany
    {
        return $this->hasMany(NotifyreSMSMessageRecipient::class, 'notifyre_sms_message_id');
    }
}

<?php

namespace Arbi\Notifyre\Models;

use Arbi\Notifyre\Models\JunctionTables\NotifyreSMSMessageRecipient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotifyreRecipients extends Model
{
    protected $fillable = [
        'type',
        'value',
    ];

    protected $casts = [
        'type' => 'string',
        'value' => 'string',
    ];

    public function notifyreSMSMessageRecipients(): HasMany
    {
        return $this->hasMany(NotifyreSMSMessageRecipient::class, 'notifyre_recipient_id');
    }
}

<?php

namespace MagicSystemsIO\Notifyre\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MagicSystemsIO\Notifyre\Models\JunctionTables\NotifyreSMSMessageRecipient;

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

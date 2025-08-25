<?php

namespace MagicSystemsIO\Notifyre\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MagicSystemsIO\Notifyre\Models\JunctionTables\NotifyreSMSMessageRecipient;

class NotifyreRecipients extends Model
{
    use HasFactory;

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
        return $this->hasMany(NotifyreSMSMessageRecipient::class, 'recipient_id');
    }
}
